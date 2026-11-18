@php($attachments = $attachments ?? collect())

@if ($attachments->isNotEmpty())
  <section class="mt-8 border-t border-gray-100 pt-6" aria-labelledby="attachments-title">
    <h2 id="attachments-title" class="mb-3 text-lg font-bold text-gray-900">Lampiran</h2>

    <div class="grid gap-2">
      @foreach ($attachments as $attachment)
        <a
          href="{{ $attachment->getUrl() }}"
          download="{{ $attachment->file_name }}"
          rel="noopener"
          title="Unduh {{ $attachment->file_name }}"
          class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm transition hover:border-green-300 hover:bg-green-50"
        >
          <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-green-700 shadow-sm">
            <i class="fas fa-paperclip" aria-hidden="true"></i>
          </span>
          <span class="min-w-0 flex-1">
            <span class="block truncate font-semibold text-gray-900">{{ $attachment->file_name }}</span>
            <span class="block text-xs text-gray-500">{{ \Illuminate\Support\Number::fileSize($attachment->size, 1) }}</span>
          </span>
          <i class="fas fa-download text-gray-400" aria-hidden="true"></i>
        </a>
      @endforeach
    </div>
  </section>
@endif
