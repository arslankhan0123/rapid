<footer class="main-footer container">
    <div class="footer-left">
        All rights reserved &copy; {{ date('Y') }}
        <a href="//{{ getWebsiteName() }}">{{ getAppName() }}</a>
    </div>
    @if(config('app.show_version'))
        <span class="float-right">v{{ version() }}</span>
    @endif
</footer>
