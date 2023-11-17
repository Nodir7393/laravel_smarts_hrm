<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Instagram</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    @livewireStyles
</head>
<body>
<div class="container">
    <div class="d-flex align-items-start">
        <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            <button class="nav-link active" id="v-pills-follow-tab" data-bs-toggle="pill" data-bs-target="#v-pills-follow" type="button" role="tab" aria-controls="v-pills-follow" aria-selected="true">Follow</button>
            <button class="nav-link" id="v-pills-unfollow-tab" data-bs-toggle="pill" data-bs-target="#v-pills-unfollow" type="button" role="tab" aria-controls="v-pills-unfollow" aria-selected="false">Unfollow</button>
            <button class="nav-link" id="v-pills-like-tab" data-bs-toggle="pill" data-bs-target="#v-pills-like" type="button" role="tab" aria-controls="v-pills-like" aria-selected="false">Like</button>
            <button class="nav-link" id="v-pills-comment-tab" data-bs-toggle="pill" data-bs-target="#v-pills-comment" type="button" role="tab" aria-controls="v-pills-comment" aria-selected="false">Comment</button>
            <button class="nav-link" id="v-pills-story-tab" data-bs-toggle="pill" data-bs-target="#v-pills-story" type="button" role="tab" aria-controls="v-pills-story" aria-selected="false">Story</button>
        </div>
        <div class="tab-content" id="v-pills-tabContent">
            <div class="tab-pane fade show active" id="v-pills-follow" role="tabpanel" aria-labelledby="v-pills-follow-tab">
                <livewire:follow-component :bot={{ json_encode($bot) }}/>
            </div>
            <div class="tab-pane fade" id="v-pills-like" role="tabpanel" aria-labelledby="v-pills-like-tab">fasdfs...</div>
            <div class="tab-pane fade" id="v-pills-unfollow" role="tabpanel" aria-labelledby="v-pills-unfollow-tab">..asdf.</div>
            <div class="tab-pane fade" id="v-pills-story" role="tabpanel" aria-labelledby="v-pills-story-tab">.fas..</div>
            <div class="tab-pane fade" id="v-pills-comment" role="tabpanel" aria-labelledby="v-pills-comment-tab">.fas..</div>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
@livewireScripts
</body>
</html>
