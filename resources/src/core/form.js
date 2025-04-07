/************************************************************************** 
 * Heroic Form
 * Utility for submitting form data without writing repetitive code
 **************************************************************************/
window.$heroicForm = function(config = {}) {
    return {
        form: {},
        init() {
            this.$el.addEventListener('submit', async (e) => {
                e.preventDefault();

                // Determine URL and method from config
                let url = config.url;
                let method = config.method;

                if (!url) {
                    if (config.postUrl) {
                        url = typeof config.postUrl === 'function' ? config.postUrl() : config.postUrl;
                        method = 'POST';
                    } else if (config.putUrl) {
                        url = typeof config.putUrl === 'function' ? config.putUrl() : config.putUrl;
                        method = 'PUT';
                    } else if (config.deleteUrl) {
                        url = typeof config.deleteUrl === 'function' ? config.deleteUrl() : config.deleteUrl;
                        method = 'DELETE';
                    } else {
                        console.error('URL not found in config.');
                        return;
                    }
                } else {
                    url = typeof url === 'function' ? url() : url;
                    method = method ?? 'POST';
                }

                if (config.confirm && !(await Prompts.confirm('Are you sure you want to proceed?'))) return;

                const formdata = new FormData(this.$el);
                const data = Object.fromEntries(formdata.entries());

                // Prepare headers
                const headers = {
                    'Content-Type': 'application/x-www-form-urlencoded'
                };

                if (config.token) {
                    headers['Authorization'] = `Bearer ${config.token}`;
                }

                try {
                    const response = await axios({
                        method,
                        url,
                        data,
                        headers,
                        transformRequest: [(data) => new URLSearchParams(data).toString()]
                    });

                    this.handleSuccess(response.data);
                } catch (error) {
                    if (error.response) {
                        this.handleError(error.response.data);
                    } else {
                        console.error('Unexpected error:', error);
                        alert('A network or server error occurred.');
                    }
                }
            });
        },
        handleSuccess(result) {
            if (typeof config.onSuccess === 'function') {
                config.onSuccess.call(this, result);
                this.$el.reset();
                return;
            }
        },
        handleError(response) {
            if (typeof config.onError === 'function') {
                config.onError.call(this, response);
            } else {
                console.error('Submit error:', response);
                alert('An error occurred while submitting the data.');
            }
        }
    }
};
