<div class="colblock">
    <h3>Google Feed</h3>
    <div class="colblock-body">
        <div class="colblock">
            <span class="bc-btn">
                <button type="button" onclick="createGoogleXml(this)">Экспортировать XML</button>
            </span>
        </div>
    </div>
</div>
<script type="text/javascript">
    async function createGoogleXml(e) {
        await fetch('/bc/modules/Korzilla/Google/GoogleFeed/UI/WEB/Controllers/createGoogleXml.php')
            .then((res) => {
                console.log(res);
            })
    }
</script>