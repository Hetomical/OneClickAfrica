@extends('common::layouts.master')

@section('comments-show')
    show
@endsection

@section('comments')
    active
@endsection

@section('comments_active')
    active
@endsection

@section('content')

    <div class="dashboard-ecommerce">
        <div class="container-fluid dashboard-content ">
            <!-- page info start-->
            <form action="#" method="post">
                <div class="admin-section">
                    <div class="row clearfix m-t-30">
                        <div class="col-12">
                            <div class="navigation-list bg-white p-20">
                                <div class="add-new-header clearfix m-b-20">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="block-header">
                                                <h2>{{ __('comments') }}</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive all-pages">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                        <tr role="row">
                                            <th>#</th>
                                            <th>{{ __('name') }}</th>
                                            <th>{{ __('email') }}</th>
                                            <th>{{ __('post') }}</th>
                                            <th>{{ __('comment') }}</th>
                                            <th>{{ __('comment_status') }}</th>
                                            <th>{{ __('comment_at') }}</th>
                                            @if(Sentinel::getUser()->hasAccess(['comments_delete']))
                                                <th>{{ __('options') }}</th>
                                            @endif
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($comments as $comment)
                                            <tr role="row" id="row_{{ $comment->id }}" class="odd">
                                                <td class="sorting_1">{{ $comment->id }}</td>
                                                <td>{{ $comment->user->first_name }}</td>
                                                <td>{{ $comment->user->email }}</td>
                                                <td>{{ $comment->post->title }}</td>
                                                <td> {{ $comment->comment }} </td>
<td>
    <label class="switch">
        <input type="checkbox" class="status-toggle" 
               data-id="{{ $comment->id }}" 
               {{ $comment->status == 1 ? 'checked' : '' }}>
        <span class="slider"></span>
    </label>
</td>

<style>

.switch {
  position: relative;
  display: inline-block;
  width: 48px;
  height: 26px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 34px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 20px;
  width: 20px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}

/* Checked state */
input:checked + .slider {
  background-color: green; /* your dark orange theme */
}

input:checked + .slider:before {
  transform: translateX(22px);
}

/* Optional hover effect */
.switch:hover .slider {
  box-shadow: 0 0 5px rgba(255, 102, 0, 0.7);
}
</style>
                                                <td>
                                                    @if($comment->created_at != null)
                                                        {{ Carbon\Carbon::parse($comment->created_at)->toDayDateTimeString() }}
                                                    @endif
                                                </td>

                                                @if(Sentinel::getUser()->hasAccess(['comments_delete']))
                                                    <td>
                                                        <a href="javascript:void(0)" class="btn btn-light active btn-xs"
                                                           onclick="delete_item('comments','{{ $comment->id }}')"><i
                                                                class="fa fa-trash"></i>
                                                            {{ __('delete') }}
                                                        </a>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <div class="block-header">
                                            <h2>{{ __('Showing') }} {{ $comments->firstItem()}} {{  __('to') }} {{ $comments->lastItem()}} {{ __('of') }} {{ $comments->total()}} {{ __('entries') }}</h2>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 text-right">
                                        <div class="table-info-pagination float-right">
                                            {!! $comments->onEachSide(1)->links() !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <!-- page info end-->
        </div>
    </div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.status-toggle').forEach(toggle => {
        toggle.addEventListener('change', function () {
            const commentId = this.dataset.id;
            const status = this.checked ? 1 : 0;

            fetch(`/post/comments/${commentId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: status })
            })
            .then(res => res.json())
            .then(data => {
                console.log(data);
                if (data.success) {
                    alert('Comment status updated successfully!');
                } else {
                    alert('Failed to update comment status.');
                }
            })
            .catch(() => alert('Error updating comment status.'));
        });
    });
});
</script>

@endsection
