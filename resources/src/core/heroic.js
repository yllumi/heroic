/************************************************************************** 
 * Page Data
 * Fungsi untuk transaksi data dasar yang dibutuhkan oleh halaman 
 * tanpa harus menulis kode yang sama berulang-ulang
 **************************************************************************/
window.$heroic = function({
    url = null, 
    title = null,
    perpage = 5,
    postUrl = null,
    postRedirect = null,
    clearCachePath = null,
    meta = {}
    } = {}) {

    return {
        // Configuration properties
        config: {
            title,
            url,
            perpage,
            postUrl,
            postRedirect,
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

        // PaginatedData data properties
        paginatedData: [],

        // Model properties
        model: {},

        // Model error messages
        modelMessage: {},

        // Function to initialize the page
        init() {
            // Set the page title
            this._setTitle();

            if(this.config.clearCachePath) {
                delete $heroicHelper.cached[this.config.clearCachePath]
            }

            // Initialize page data if requested
            if(this.config.url) {
                // Use $heroicHelper.cached data if exists
                if($heroicHelper.cached[this.config.url]) {
                    // Process for list-type data
                    if($heroicHelper.cached[this.config.url]?.paginatedData) {
                        $heroicHelper.cached[this.config.url].paginatedData.forEach(item => {
                            this.paginatedData.push(item)
                        })
                        this.ui.nextPage = $heroicHelper.cached[this.config.url].nextPage
                        this.ui.loadMore = $heroicHelper.cached[this.config.url].loadMore
                    } 
                    // Process for row-type data
                    else {
                        this.data = $heroicHelper.cached[this.config.url].data
                    }
                } else {
                    this._fetchPageData();
                }
            }

            window.scrollTo(0,0)
        },

        _fetchPageData() {
            this.ui.loading = true;
            $heroicHelper.fetch(this.config.url)
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

        reload() {
            this._fetchPageData();
        },

        assignResponseData(response, cache = true) {
            this.data = response.data;
    
            if(cache)
                $heroicHelper.cached[this.config.url] = this.data;
        },

        loadMore() {
            this._fetchPaginatedData(this.ui.nextPage)
        },

        _fetchPaginatedData(page) {
            this.ui.loading = true;
            $heroicHelper.fetch(this.config.url + `?page=` + page)
            .then(response => {
                if(response.response_code == 200) {
                    // Check if response data is a paginatedData
                    if(response.paginatedData.length > 0) {
                        this.ui.nextPage += 1;
                        response.paginatedData.forEach(item => {
                            this.paginatedData.push(item)
                        })
                    } else {
                        this.ui.empty = true;
                        this.ui.nextPage = null;
                        this.ui.loadMore = false;
                    }
                    // Save response data to cache
                    let cached = {paginatedData: this.paginatedData, nextPage: this.ui.nextPage, loadMore: this.ui.loadMore}
                    $heroicHelper.cached[this.config.url] = cached;
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

        _setTitle() {
            if(this.config.title){
                document.title = this.config.title;
            }
        },

        async submitData(params = {}) {
            if(params?.confirm) {
                const confirmedBoolean = await Prompts.confirm(params.confirm);
                if (!confirmedBoolean) return;
            }

            this.ui.submitting = true
            this.modelMessage = {}
            $heroicHelper.post(this.config.postUrl, this.model)
            .then(response => {
                if(response.status == 200) {
                    if(this.config.postRedirect) {
                        delete $heroicHelper.cached[this.config.clearCachePath]
                        window.PineconeRouter.context.redirect(this.config.postRedirect)
                    } else {
                        this.model = {}
                        $heroicHelper.toastr('Data saved', 'success', 'bottom');
                    }
                } else {
                    this.modelMessage = response.data.model_messages
                }
            })
        
        }
    }
}
