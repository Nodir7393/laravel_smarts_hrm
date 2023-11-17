<div class="d-flex justify-content-between mb-2">
    <label class="mr-3" for="{{$name}}">{{$label}}</label>
    <select class="js-example-basic-single w-{{$w}}" name="{{$name}}" {{$m}}>
        @foreach (json_decode($options) as $key =>  $elem)
        <option value="{{$key}}">{{$elem}}</option>
        @endforeach
    </select>
</div>

<script>
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
    });
</script>
