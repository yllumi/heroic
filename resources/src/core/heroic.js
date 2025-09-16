/************************************************************************** 
 * Page Data
 * Fungsi untuk transaksi data dasar yang dibutuhkan oleh halaman 
 * tanpa harus menulis kode yang sama berulang-ulang
 **************************************************************************/
window.$heroic = function({
    title = null,
    url = null, 
    clearCachePath = false,
    headers = {},
    meta = {}
    } = {}) {

    return {
        // Configuration properties
        config: {
            title,
            url,
            headers,
            clearCachePath
        },

        // UI properties
        ui: {
            loading: false,
            submitting: false,
            empty: false,
            nextPage: null,
            loadMore: false,
            error: false,
            errorMessage: '',
        },

        // Raw data and meta properties
        data: {},

        // Another custom data set by user
        meta: meta,

        // Function to initialize the page
        init() {
            // Set the page title
            this._setTitle();

            if(this.config.clearCachePath) {
                // Clear cached data
                $heroicHelper.clearCache(this.config.clearCachePath);
            }

            this.loadPage();
        },

        loadPage(fetchUrl = null) {
            // Prevent same url fetch many times if already loaded
            if(fetchUrl == this.config.url) return;
            
            this.config.url = fetchUrl ?? this.config.url;

            // Initialize page data if requested
            if(this.config.url) {
                // Use cached data if exists
                if($heroicHelper.getCache(this.config.url)) {
                    this.data = $heroicHelper.getCache(this.config.url);
                } else {
                    this.fetchData();
                }
            }
            window.scrollTo(0,0);
        },

        fetchData() {
            this.ui.loading = true;
            $heroicHelper.fetch(this.config.url, this.config.headers)
            .then(response => {
                if(response.status == 200) {
                    this.assignResponseData(response)
                } else {
                    this.ui.error = true;
                    this.ui.errorMessage = response.message;
                }
            })
            .catch(error => {
                this.ui.error = true;
                console.error('Error fetching page data:', error);
            })
            .finally(() => {
                this.ui.loading = false;
            });
        },

        assignResponseData(response, cache = true) {
            this.data = response.data;
    
            if(cache)
                $heroicHelper.setCache(this.config.url, this.data);
        },

        _setTitle() {
            if(this.config.title){
                document.title = this.config.title;
            }
        },
    }
}
