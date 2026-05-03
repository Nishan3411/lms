<x-app-layout>
    <x-slot name="header">
        <div class="module-header">
            <div>
                <h2 class="module-title">
                    Learning Materials
                </h2>
                <p class="module-copy">
                    Download PDF and presentation files shared by your teachers.
                </p>
            </div>

            <a href="{{ route('student.dashboard') }}" class="secondary-action">
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="module-page">
        <div class="module-shell">
            <div class="module-card">
                <h3 class="text-lg font-semibold text-slate-900">Available Materials</h3>
                <p class="text-sm text-slate-500">These files are available because you are enrolled in the related classes.</p>

                <div class="mt-5 space-y-4">
                    @forelse ($materials as $material)
                        <div class="module-subcard !rounded-[24px] !p-5">
                            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <h4 class="font-semibold text-slate-900">{{ $material->title }}</h4>
                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $material->classRoom->name }}
                                        @if ($material->subject)
                                            · {{ $material->subject->name }}
                                        @endif
                                        · Uploaded by {{ $material->teacher->name }}
                                    </p>
                                    <p class="mt-2 text-sm text-slate-600">{{ $material->description ?: 'No description provided.' }}</p>
                                    <p class="mt-2 text-xs text-slate-500">
                                        {{ $material->original_filename }} · {{ number_format($material->file_size / 1024, 1) }} KB
                                    </p>
                                </div>

                                <a href="{{ route('student.materials.download', $material) }}" class="inline-flex items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                                    Download
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No learning materials are available yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
