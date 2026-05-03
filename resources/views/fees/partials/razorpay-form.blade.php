@php
    $gatewayConfigured = filled(config('services.razorpay.key_id')) && filled(config('services.razorpay.key_secret'));
    $amountValue = number_format((float) $amount, 2, '.', '');
    $showAmountInput = $showAmountInput ?? true;
    $buttonLabel = $buttonLabel ?? 'Pay with Razorpay';
    $helperText = $helperText ?? 'Payments are securely processed through Razorpay.';
@endphp

<form
    class="razorpay-pay-form"
    data-razorpay-form
    data-create-url="{{ $createUrl }}"
    data-verify-url-template="{{ $verifyUrlTemplate }}"
>
    @if ($showAmountInput)
        <label class="auth-field">
            <span class="auth-label">{{ $amountLabel ?? 'Amount to pay' }}</span>
            <input
                type="number"
                name="amount"
                class="auth-input"
                min="1"
                max="{{ $maxAmount ?? $amountValue }}"
                step="0.01"
                value="{{ $amountValue }}"
                required
            >
        </label>
    @else
        <input type="hidden" name="amount" value="{{ $amountValue }}">
    @endif

    <div class="mt-4 flex flex-wrap items-center gap-3">
        <button
            type="submit"
            class="primary-action"
            data-razorpay-submit
            @disabled(! $gatewayConfigured)
        >
            {{ $gatewayConfigured ? $buttonLabel : 'Razorpay Not Configured' }}
        </button>

        @if (! $showAmountInput)
            <span class="data-pill">Amount {{ number_format((float) $amount, 2) }}</span>
        @endif
    </div>

    <p class="mt-3 text-sm text-slate-500">
        @if ($gatewayConfigured)
            {{ $helperText }}
        @else
            Add `RAZORPAY_KEY_ID` and `RAZORPAY_KEY_SECRET` to enable online fee payments.
        @endif
    </p>

    <p data-razorpay-feedback class="mt-3 hidden text-sm font-medium"></p>
</form>
