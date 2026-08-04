@extends('layouts.public')

@section('title', 'Announcements | Armely')
@section('page-title', 'Announcements')

@section('content')
    @if($dbErrorMessage)
        <p>{{ $dbErrorMessage }}</p>
    @endif

    @if($announcement)
        <iframe
            id="announcementFrame"
            srcdoc="{{ $announcement->body_html }}"
            style="display:block;width:100%;border:0;overflow:hidden;background:transparent;"
            loading="eager"
            title="Announcement content"
        ></iframe>
        <script>
            (function () {
                var frame = document.getElementById('announcementFrame');
                if (!frame) return;

                function resizeFrame() {
                    try {
                        var doc = frame.contentDocument || frame.contentWindow.document;
                        if (!doc || !doc.body) return;
                        var height = Math.max(
                            doc.body.scrollHeight || 0,
                            doc.documentElement.scrollHeight || 0,
                            doc.body.offsetHeight || 0,
                            doc.documentElement.offsetHeight || 0
                        );
                        if (height) {
                            frame.style.height = height + 'px';
                        }
                    } catch (error) {
                        frame.style.height = '100vh';
                    }
                }

                frame.addEventListener('load', function () {
                    resizeFrame();
                    setTimeout(resizeFrame, 100);
                    setTimeout(resizeFrame, 500);
                });

                window.addEventListener('resize', resizeFrame);
            })();
        </script>
    @else
        <p>No announcements available yet.</p>
    @endif
@endsection
