@extends('voyager::master')

@section('content')
    <form class="form-group" method="post" action="{{ route('create.job') }}">
        @csrf
        <h1>Create job</h1>
        <label for="job_name">Job name</label>
        <input id="job_name" name="job_name" class="form-control">
        <label for="user">Insta user</label>
        <select class="form-control" id="user" name="insta_user_id">
            <option value="0"> -- select user -- </option>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->username }}</option>
            @endforeach
        </select>
        <label for="bot">Insta bot</label>
        <select class="form-control" id="bot" name="insta_bot_id" required>
            <option value="0"> -- select bot -- </option>
            @foreach($bots as $bot)
                <option value="{{ $bot->id }}">{{ $bot->user_name }}</option>
            @endforeach
        </select>
        <label for="type">Type</label>
        <select class="form-control" id="type" name="type">
            <option value="0"> -- select type -- </option>
            <option value="AutoAcceptJob">AutoAcceptJob</option>
            <option value="CommentPostsJob">CommentPostsJob</option>
            <option value="CommentRandomJob">CommentRandomJob</option>
            <option value="FollowJob">FollowJob</option>
            <option value="GetPostsJob">GetPostsJob</option>
            <option value="LikeOnePostJob">LikeOnePostJob</option>
            <option value="LikePostsJob">LikePostsJob</option>
            <option value="LikeRandomJob">LikeRandomJob</option>
            <option value="LikeTagSearchJob">LikeTagSearchJob</option>
            <option value="ParseUserJob">ParseUserJob</option>
            <option value="StoryViewJob">StoryViewJob</option>
            <option value="TagSearchJob">TagSearchJob</option>
            <option value="TagUsersJob">TagUsersJob</option>
            <option value="UnfollowJob">UnfollowJob</option>
        </select>

        <label for="text">Text</label>
        <input id="text" type="text" class="form-control" name="text">

        <label for="count">Count</label>
        <input id="count" name="count" class="form-control">
        <button class="btn btn-primary" type="submit">Submit</button>
    </form>
    <form class="form-group" method="post" action="{{ route('select.job') }}">
        @csrf
        <h1>Run job</h1>
        <label for="job">Jobs</label>
        <select class="form-control" id="job" name="job">
            @foreach ($jobs as $job)
                <option value="{{ $job->id }}">{{ $job->name }}</option>
            @endforeach
        </select>
        <button class="btn btn-primary" type="submit">Submit</button>
    </form>
@endsection
