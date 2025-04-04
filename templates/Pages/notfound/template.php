<div
    id="notfound"
    x-data="$heroic({
        title: `<?= $page_title ?>`,
        getUrl: `/notfound/data`
        })">

    <div class="appHeader bg-brand">
        <div class="left"></div>
        <div class="pageTitle text-white" x-text="data.page_title"></div>
        <div class="right"></div>
    </div>

    <div id="appCapsule">

        <div class="text-center py-3">
        
            <svg width="100%" height="100px" viewBox="0 0 400 200" xmlns="http://www.w3.org/2000/svg" fill="none">
            <style>
                .main { fill: #ff6b00; font-family: 'Segoe UI', sans-serif; font-weight: 900; }
                .shadow { fill: #00000020; }
            </style>
            <text x="50%" y="52%" text-anchor="middle" font-size="260" class="shadow">404</text>
            <text x="50%" y="50%" text-anchor="middle" font-size="240" class="main">404</text>
            </svg>

            <h2>Page not found</h2>
            <a href="/">Go to home</a>
            
        </div>

    </div>
</div>