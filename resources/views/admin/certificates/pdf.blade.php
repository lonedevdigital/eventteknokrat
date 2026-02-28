@php
    $fields = $template->config['fields'] ?? [];

    $getField = function($key) use ($fields) {
        return $fields[$key] ?? null;
    };

    $renderField = function($key, $text) use ($getField) {
        $field = $getField($key);
        if (!$field) return;

        $x = $field['x'] ?? 50; // persen
        $y = $field['y'] ?? 50; // persen
        $fontSize = $field['font_size'] ?? 24;
        $align = $field['align'] ?? 'center';
@endphp
<div class="field"
     style="
                top: {{ $y }}%;
                left: {{ $x }}%;
                font-size: {{ $fontSize }}pt;
                text-align: {{ $align }};
             ">
    {{ $text }}
</div>
@php
    };
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
        }
        .page {
            position: relative;
            width: 100%;
            height: 100%;
        }
        .bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .field {
            position: absolute;
            transform: translate(-50%, -50%);
            font-family: DejaVu Sans, sans-serif; /* aman buat Dompdf */
            color: #000000;
            white-space: nowrap;
        }
    </style>
</head>
<body>
<div class="page">
    {{-- background template --}}
    <img class="bg" src="{{ public_path($template->background_path) }}" alt="bg">

    {{-- panggil field satu-satu --}}
    {{ $renderField('certificate_number', $data['certificate_number'] ?? '') }}
    {{ $renderField('recipient_label', $data['recipient_label'] ?? '') }}
    {{ $renderField('participant_name', $data['participant_name'] ?? '') }}
    {{ $renderField('role', $data['role'] ?? '') }}
    {{ $renderField('event_title', $data['event_title'] ?? '') }}
    {{ $renderField('event_theme', $data['event_theme'] ?? '') }}
    {{ $renderField('event_date', $data['event_date'] ?? '') }}
    {{ $renderField('signature_name', $data['signature_name'] ?? '') }}
    {{ $renderField('signature_title', $data['signature_title'] ?? '') }}
</div>
</body>
</html>
