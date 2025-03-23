/************************************************************************** 
 * Page Data
 * Fungsi untuk transaksi data dasar yang dibutuhkan oleh halaman 
 * tanpa harus menulis kode yang sama berulang-ulang
 **************************************************************************/
window.$heroic = function({
    getUrl = null, 
    title = null,
    perpage = 5,
    postUrl = null,
    postRedirect = null,
    clearCachePath = null
    } = {}) {

    return {
        // Configuration properties
        config: {
            title,
            getUrl,
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

        // Raw data properties
        data: {},

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
            if(this.config.getUrl) {
                // Use $heroicHelper.cached data if exists
                if($heroicHelper.cached[this.config.getUrl]) {
                    // Process for list-type data
                    if($heroicHelper.cached[this.config.getUrl]?.paginatedData) {
                        $heroicHelper.cached[this.config.getUrl].paginatedData.forEach(item => {
                            this.paginatedData.push(item)
                        })
                        this.ui.nextPage = $heroicHelper.cached[this.config.getUrl].nextPage
                        this.ui.loadMore = $heroicHelper.cached[this.config.getUrl].loadMore
                    } 
                    // Process for row-type data
                    else {
                        this.data = $heroicHelper.cached[this.config.getUrl].data
                    }
                } else {
                    this._fetchPageData();
                }
            }

            window.scrollTo(0,0)
        },

        _fetchPageData() {
            this.ui.loading = true;
            $heroicHelper.fetch(this.config.getUrl)
            .then(response => {
                if(response.response_code == 200) {
                    // Check if response data is a paginatedData
                    if(response?.paginatedData) {
                        this.ui.nextPage = 2
                        this.ui.loadMore = true
                        response.paginatedData.forEach(item => {
                            this.paginatedData.push(item)
                        })
                        // Save response data to cache
                        let cached = {paginatedData: this.paginatedData, nextPage: this.ui.nextPage, loadMore: this.ui.loadMore}
                        $heroicHelper.cached[this.config.getUrl] = cached;
                    } else {
                        this.data = response.data;
                        let cached = {data: this.data}
                        $heroicHelper.cached[this.config.getUrl] = cached;
                    }

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

        loadMore() {
            this._fetchPaginatedData(this.ui.nextPage)
        },

        _fetchPaginatedData(page) {
            this.ui.loading = true;
            $heroicHelper.fetch(this.config.getUrl + `?page=` + page)
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
                    $heroicHelper.cached[this.config.getUrl] = cached;
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
            .then(data => {
                if(data.response_code == 200) {
                    if(this.config.postRedirect) {
                        delete $heroicHelper.cached[this.config.clearCachePath]
                        window.PineconeRouter.context.redirect(this.config.postRedirect)
                    } else {
                        this.model = {}
                        $heroicHelper.toastr('Data saved', 'success', 'bottom');
                    }
                } else {
                    this.modelMessage = data.model_messages
                }
            })
        
        }
    }
}
