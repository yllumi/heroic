<div
    id="{{pageSlug}}"
    x-data="$heroic({
        title: `<?= $page_title ?>`,
        getUrl: `/{{pagePath}}/data`
        })">

    <div class="appHeader bg-brand">
        <div class="left"></div>
        <div class="pageTitle text-white" x-text="data.page_title"></div>
        <div class="right"></div>
    </div>

    <div id="appCapsule">

        <div class="text-center py-3">
            <h1>Welcome <span x-text="data.name"></span>!</h1>
        </div>

    </div>
</div>