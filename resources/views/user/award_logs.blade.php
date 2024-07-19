@extends('user.layout')

@section('profile-title') {{ $user->name }}'s {{ucfirst(__('awards.award'))}} Logs @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, ucfirst(__('awards.awardcase')) => $user->url . '/'.__('awards.awardcase'), 'Logs' => $user->url.'/award-logs']) !!}

<h1>
    {!! $user->displayName !!}'s {{ucfirst(__('awards.award'))}} Logs
</h1>

{!! $logs->render() !!}
<div class="row ml-md-2 mb-4">
  <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
    <div class="col-6 col-md-2 font-weight-bold">Sender</div>
    <div class="col-6 col-md-2 font-weight-bold">Recipient</div>
    <div class="col-6 col-md-2 font-weight-bold">{{ucfirst(__('awards.award'))}}</div>
    <div class="col-6 col-md-4 font-weight-bold">Log</div>
    <div class="col-6 col-md-2 font-weight-bold">Date</div>
  </div>
    @foreach($logs as $log)
        @include('user._award_log_row', ['log' => $log, 'owner' => $user])
    @endforeach
</div>
{!! $logs->render() !!}

@endsection
