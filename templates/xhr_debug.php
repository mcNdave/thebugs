<script>
    (function(send) {
        XMLHttpRequest.prototype.send = function(data) {
            console.log("Hey ! Something was sent !");
            send.call(this, data);
        };
    })(XMLHttpRequest.prototype.send);

    class ErrorHandler
    {
        constructor(options) {
            if ( options ) {
                if ( "url" in options ) {
                    this.url = options['url'];
                }
            }

            this.catchError();
        }

        catchError() {
            window.onerror = function(message, url, line, column, error) {
                fetch(this.url ? this.url : window.location.href, {
                    method: "post",
                    headers: {
                        'Accept': "application/json",
                        'Content-Type': "application/json",
                        'User-Agent': "TheBugs/1.0"
                    },
                    body: JSON.stringify({
                        'message': message,
                        'url': url,
                        'line': line,
                        'column': column,
                        'stack': error.stack,
                        'location': window.location.toString()
                    })
                }).then( response => response ).then(data => {
                    console.info("Error reported", data);
                });

                return false;
            }.bind(this);
        }

        get url() {
            return this._url;
        }

        set url(set) {
            return this._url = set;
        }
    }
</script>
