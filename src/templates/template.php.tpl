<div
    id="{{pageSlug}}"
    x-data="$heroic({
        title: `<?= $page_title ?>`,
        getUrl: `/{{pagePath}}/data`
        })">

    <div class="text-center py-3">
        <h1>Welcome <span x-text="data.name"></span>!</h1>
    </div>

</div>
