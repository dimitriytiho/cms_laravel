<p style="font-size: 16px">{!! $body !!}</p>
{{--<p style="background-color: #777; color: #fff; font-size: 16px; padding: 20px 30px;">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci aliquam dignissimos dolorem earum eius esse est et eveniet, fugit hic in ipsam, labore libero magni minus molestiae necessitatibus neque nihil odio officiis optio pariatur provident quaerat quo repellat repellendus rerum saepe sapiente soluta tempora temporibus unde vel voluptatem voluptates voluptatum.</p>--}}
@if (isset($values) && is_array($values))
    <ul>
        @foreach($values as $v)
            <li>{{ $v }}</li>
        @endforeach
    </ul>
@endif
