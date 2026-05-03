const razorpayForms = () => document.querySelectorAll('[data-razorpay-form]');

let razorpayScriptPromise;

const loadRazorpayScript = () => {
    if (window.Razorpay) {
        return Promise.resolve();
    }

    if (razorpayScriptPromise) {
        return razorpayScriptPromise;
    }

    razorpayScriptPromise = new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = 'https://checkout.razorpay.com/v1/checkout.js';
        script.async = true;
        script.onload = resolve;
        script.onerror = () => reject(new Error('Unable to load Razorpay checkout.'));
        document.head.appendChild(script);
    });

    return razorpayScriptPromise;
};

const setFeedback = (form, message, tone = 'error') => {
    const feedback = form.querySelector('[data-razorpay-feedback]');

    if (!feedback) {
        return;
    }

    feedback.textContent = message;
    feedback.classList.remove('hidden', 'text-emerald-700', 'text-rose-600');
    feedback.classList.add(tone === 'success' ? 'text-emerald-700' : 'text-rose-600');
};

const resetButton = (button, label) => {
    button.disabled = false;
    button.textContent = label;
};

const initializeRazorpayCheckout = () => {
    const forms = razorpayForms();

    if (!forms.length) {
        return;
    }

    forms.forEach((form) => {
        if (form.dataset.razorpayBound === 'true') {
            return;
        }

        form.dataset.razorpayBound = 'true';

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const submitButton = form.querySelector('[data-razorpay-submit]');

            if (!submitButton) {
                return;
            }

            const defaultLabel = submitButton.textContent.trim();
            const amountInput = form.querySelector('input[name="amount"]');
            const amount = amountInput ? amountInput.value : '';

            setFeedback(form, '');
            form.querySelector('[data-razorpay-feedback]')?.classList.add('hidden');

            submitButton.disabled = true;
            submitButton.textContent = 'Preparing payment...';

            try {
                const orderResponse = await window.axios.post(form.dataset.createUrl, {
                    amount,
                }, {
                    headers: {
                        Accept: 'application/json',
                    },
                });

                await loadRazorpayScript();

                const order = orderResponse.data;

                const checkout = new window.Razorpay({
                    key: order.key,
                    amount: order.amount,
                    currency: order.currency,
                    name: order.name,
                    description: order.description,
                    order_id: order.order_id,
                    prefill: order.prefill,
                    notes: order.notes,
                    theme: {
                        color: '#0f766e',
                    },
                    handler: async (response) => {
                        submitButton.textContent = 'Verifying payment...';

                        try {
                            const verifyUrl = form.dataset.verifyUrlTemplate.replace('__ATTEMPT__', order.attempt_id);

                            const verifyResponse = await window.axios.post(verifyUrl, response, {
                                headers: {
                                    Accept: 'application/json',
                                },
                            });

                            setFeedback(form, verifyResponse.data.message, 'success');
                            window.location.assign(verifyResponse.data.redirect_url);
                        } catch (error) {
                            const message = error?.response?.data?.message
                                ?? error?.response?.data?.errors?.payment?.[0]
                                ?? 'Payment verification failed.';

                            setFeedback(form, message);
                            resetButton(submitButton, defaultLabel);
                        }
                    },
                    modal: {
                        ondismiss: () => {
                            resetButton(submitButton, defaultLabel);
                        },
                    },
                });

                checkout.on('payment.failed', (response) => {
                    const message = response?.error?.description ?? 'Payment failed. Please try again.';
                    setFeedback(form, message);
                    resetButton(submitButton, defaultLabel);
                });

                checkout.open();
            } catch (error) {
                const message = error?.response?.data?.message
                    ?? error?.response?.data?.errors?.payment?.[0]
                    ?? 'Unable to start Razorpay payment right now.';

                setFeedback(form, message);
                resetButton(submitButton, defaultLabel);
            }
        });
    });
};

initializeRazorpayCheckout();
