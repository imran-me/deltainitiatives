<x-layouts.public :title="'সেবা'" :description="'টেক সাপোর্ট, ব্যবসায়িক সিস্টেম, কাস্টম সফটওয়্যার, প্রোটোটাইপ, ব্র্যান্ড এবং ডেল্টা এক্সপার্ট নেটওয়ার্ক।'">
<x-page-hero :eyebrow="'সেবা'" :title="'বাংলাদেশ যা দিয়ে চলে, আমরা তা গড়ি।'" :intro="'ছয়টি সেবা, কাজের একটি ধরন। আমরা যা তৈরি করি, সবই আপনার।'">
    <a class="btn pri" href="{{ route('services.index') }}#proposal">প্রস্তাবনা চান</a>
</x-page-hero>
<section class="sec"><div class="wrap"><div class="grid g3">
@foreach([
    ['bn.services.tech-support', 'টেক সাপোর্ট', 'দ্রুত সাড়া, স্পষ্ট দাম, নির্ভরযোগ্য আইটি সহায়তা।'],
    ['bn.services.business-systems', 'ব্যবসায়িক সিস্টেম', 'ERP ও অভ্যন্তরীণ সিস্টেম, যা আপনার কাজের সঙ্গে মেলে।'],
    ['bn.services.custom-software', 'কাস্টম সফটওয়্যার', 'আপনার সমস্যার জন্য তৈরি ওয়েব ও মোবাইল সফটওয়্যার।'],
    ['bn.services.prototypes', 'প্রোটোটাইপ', 'ছোট করে গড়ুন, দ্রুত যাচাই করুন।'],
    ['bn.services.brand', 'ব্র্যান্ড', 'পরিচয়, ওয়েবসাইট ও বিষয়বস্তু, যা আস্থা তৈরি করে।'],
    ['bn.services.expert-network', 'এক্সপার্ট নেটওয়ার্ক', 'প্রকল্পের জন্য যাচাই করা বিশেষজ্ঞ।'],
] as [$r, $t, $d])
<a class="card rv" href="{{ route($r) }}"><h3>{{ $t }}</h3><p>{{ $d }}</p><p class="go">বিস্তারিত →</p></a>
@endforeach
</div></div></section>
</x-layouts.public>
