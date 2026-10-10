@if($event->tanggal_pelaksanaan)
    @php
        $timezone = config('app.timezone');

        $startDate = \Carbon\Carbon::parse((string) $event->tanggal_pelaksanaan, $timezone);
        if ($event->waktu_pelaksanaan) {
            try {
                $time = \Carbon\Carbon::parse((string) $event->waktu_pelaksanaan, $timezone)->format('H:i:s');
                $startDate = \Carbon\Carbon::parse($startDate->format('Y-m-d') . ' ' . $time, $timezone);
            } catch (\Throwable $e) {
                // biarkan startDate tanpa jam jika format waktu tidak valid
            }
        }

        $endDate = $event->tanggal_pelaksanaan_selesai
            ? \Carbon\Carbon::parse((string) $event->tanggal_pelaksanaan_selesai, $timezone)
            : null;
        if ($endDate && $event->waktu_pelaksanaan_selesai) {
            try {
                $time = \Carbon\Carbon::parse((string) $event->waktu_pelaksanaan_selesai, $timezone)->format('H:i:s');
                $endDate = \Carbon\Carbon::parse($endDate->format('Y-m-d') . ' ' . $time, $timezone);
            } catch (\Throwable $e) {
                // biarkan endDate tanpa jam jika format waktu tidak valid
            }
        }

        $schemaImage = $coverBig ?? $thumb ?? asset('logo_full.png');

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $event->nama_event,
            'description' => $event->metaDescription(300),
            'startDate' => $startDate->toIso8601String(),
            'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
            'eventStatus' => 'https://schema.org/EventScheduled',
            'image' => [$schemaImage],
            'location' => [
                '@type' => 'Place',
                'name' => $event->tempat_pelaksanaan ?: 'Universitas Teknokrat Indonesia',
            ],
            'organizer' => [
                '@type' => 'Organization',
                'name' => 'Universitas Teknokrat Indonesia',
                'url' => url('/'),
            ],
        ];

        if ($endDate) {
            $schema['endDate'] = $endDate->toIso8601String();
        }
    @endphp
    @push('schema')
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush
@endif
