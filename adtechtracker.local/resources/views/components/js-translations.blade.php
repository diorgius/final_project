<script>
    window.translations = {{Illuminate\Support\Js:: from([
        'advertiser' => __('users.advertiser'),
        'offer_name' => __('offers.offer_name'),
        'offer_theme' => __('offers.offer_theme'),
        'offer_price' => __('offers.offer_price'),
        'subscribers' => __('offers.subscribers'),
        'referral_link' => __('offers.referral_link'),
        'title_for_js' => __('statistics.title_for_js'),
        'title_for_js_on' => __('statistics.title_for_js_on'),
        'day' => __('statistics.day'),
        'month' => __('statistics.month'),
        'year' => __('statistics.year'),
        'alltime' => __('statistics.alltime'),
        'active' => __('statistics.active'),
        'deactive' => __('statistics.deactive'),
        'deleted' => __('statistics.deleted'),
    ]) }};

    window.__ = function (key) {
        return window.translations[key] ?? key;
    };
</script>