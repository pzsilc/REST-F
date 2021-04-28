@foreach($messages as $message)
    @if($message['type'] == 'success')
        <div class="message btn btn-success">{{ $message['text'] }}</div>
    @elseif($message['type'] == 'error') 
        <div class="message btn btn-danger">{{ $message['text'] }}</div>
    @elseif($message['type'] == 'info')
        <div class="message btn btn-info">{{ $message['text'] }}</div>
    @endif
@endforeach