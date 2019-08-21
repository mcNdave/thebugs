<script>
    (send => {
        XMLHttpRequest.prototype.send = function() {
            this.addEventListener('load', function(e) {
                switch(this.status) {
                    case 500:
                        let response = JSON.parse(this.response),
                            content = JSON.stringify(response.error.trace, null, 2),
                            element = document.createElement('div'),
                            body = document.querySelector("body");

                        body.appendChild(element);
                        element.outerHTML = `
                            <div ondblclick="this.parentNode.removeChild(this);">
                                <dialog style="top:10vh;width:75%;z-index:1500;background:#444;color:#444;border:0px solid #2f2a2a;padding:2px;position:fixed; max-height:80vh;">
                                    <div style="background:#fff">
                                        <div style="padding:15px; color:#fff; border-bottom:2px solid #444;background:#c61f1f;font-weight:bold">${response.error.message}</div>
                                        <pre style="overflow-y:auto;max-height:65vh;padding:15px;">${content}</pre>
                                        <div style="padding:8px 15px; color:#fff; border-top:2px solid #444;text-align:right;background:#c61f1f;font-weight:bold">${response.error.file}:${response.error.line}</div>
                                    </div>
                                </dialog>
                                <div style="position:fixed;top:0;left:0;width:100vw;height:100vh;background:#000;opacity:0.5"></div>
                            </div>
                        `.trim();

                        body.querySelector("dialog").showModal();
                        break;
                }
            });

            send.apply(this, arguments);
        };
    })(XMLHttpRequest.prototype.send);
</script>
