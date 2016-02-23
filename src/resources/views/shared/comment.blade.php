<div class="panel panel-default" style="margin:10px;">
    <div class="panel-heading" style="color:#333;background-color:initial" role="tab" id="comment-heading{{$comment->id}}"
         id="comment-heading{{$comment->id}}">
        <h4 class="panel-title" style="background:#{{  $color }}">
            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#{{str_slug($comment->title)}}" aria-expanded="false" ariacontrols="{{str_slug($comment->title)}}">
                {{ $comment->user->name }}
            </a>
            <div class="ticket-action close-ticket"><i
                        class="fa fa-times"></i></div>
            <div class="ticket-action edit-ticket"><i
                        class="fa fa-pencil"></i></div>
            <div style="width:calc(100% - 20px);margin-top:-15px;margin-left:-5px;">
                <span class="badge customize">Created: {{ date('M d, Y H:i', strtotime($comment->created_at)) . ' '. $comment->id }}</span>
            </div>
        </h4>
        <div class="collapsible-body @if(config('app.debug')) themer--accent-2 @endif"
             style="display:block;font-size:1.5rem">
            <p>{{$comment->body}}</p>
        </div>
    </div>
</div>