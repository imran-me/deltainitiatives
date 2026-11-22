@php $bn = app()->getLocale() === 'bn'; @endphp
<svg class="eon-hero-svg" viewBox="0 0 440 420" role="img" aria-labelledby="eon-h-t eon-h-d">
    <title id="eon-h-t">{{ $bn ? 'ইওন কীভাবে কাজ করে' : 'How EON works' }}</title>
    <desc id="eon-h-d">{{ $bn ? 'ব্যবহারকারী মুখে বা লিখে প্রশ্ন করেন। ইওন প্রশ্ন বোঝে, ERP পড়ে, তারপর সংখ্যা, প্রযোজ্য নিয়ম ও প্রস্তাবিত করণীয় সহ উত্তর দেয়। নিশ্চিতকরণ ছাড়া কিছু সংরক্ষণ হয় না।' : 'A user asks by voice or text. EON understands the question, reads the ERP, then answers with the numbers, the rule applied and a recommended action. Nothing is saved without confirmation.' }}</desc>
    <path class="eon-flow" d="M220 92V112M220 182V202M220 272V292" fill="none" stroke="#0B4F9C" stroke-width="2.5" stroke-linecap="round" stroke-dasharray="5 6"/>
    @php
        $steps = $bn
            ? [['প্রশ্ন', 'মুখে বা লিখে · বাংলা, ইংরেজি, বাংলিশ'], ['বোঝা', 'কোন তথ্য, কোন নিয়ম, কোন সময়'], ['ERP পড়া', 'হিসাব, স্টক, উপস্থিতি'], ['উত্তর', 'সংখ্যা, কারণ, প্রস্তাবিত করণীয়']]
            : [['Ask', 'Voice or text · Bangla, English, Banglish'], ['Understand', 'Which data, which rule, which period'], ['Read the ERP', 'Accounts, stock, attendance'], ['Answer', 'Numbers, reason, recommended action']];
    @endphp
    @foreach($steps as $i => [$h, $sub])
        @php $y = 12 + $i * 90; $last = $i === 3; @endphp
        <g class="eon-step">
            <rect x="40" y="{{ $y }}" width="360" height="80" rx="16" fill="{{ $last ? '#0B4F9C' : '#E8F1FB' }}" stroke="#0B4F9C" stroke-width="1.5"/>
            <circle cx="76" cy="{{ $y + 40 }}" r="14" fill="{{ $last ? '#fff' : '#0B4F9C' }}"/>
            <text x="76" y="{{ $y + 45 }}" text-anchor="middle" font-size="14" font-weight="700" fill="{{ $last ? '#0B4F9C' : '#fff' }}">{{ $bn ? bn_digits($i + 1, true) : $i + 1 }}</text>
            <text x="104" y="{{ $y + 36 }}" font-size="18" font-weight="700" fill="{{ $last ? '#fff' : '#0B3A75' }}">{{ $h }}</text>
            <text x="104" y="{{ $y + 58 }}" font-size="12.5" fill="{{ $last ? '#fff' : '#0B3A75' }}">{{ $sub }}</text>
        </g>
    @endforeach
    <rect x="40" y="378" width="360" height="30" rx="15" fill="#fff" stroke="#006A4E"/>
    <text x="220" y="398" text-anchor="middle" font-size="12.5" font-weight="700" fill="#006A4E">{{ $bn ? 'আপনার নিশ্চিতকরণ ছাড়া কিছু সংরক্ষণ বা পাঠানো হয় না' : 'Nothing is saved or sent without your confirmation' }}</text>
</svg>
