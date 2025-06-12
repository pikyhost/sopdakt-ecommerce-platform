@php
    if (!isset($attributes['id'])) {
        $attributes['id'] = $attributes['name'];
    }
    if (!isset($attributes['label-name'])) {
        $attributes['label-name'] = __(ucwords(str_replace('_', ' ', $attributes['name'])));
    }
    if (!isset($attributes['type'])) {
        $attributes['type'] = 'text';
    }
    if (!isset($attributes['placeholder'])) {
        $attributes['placeholder'] = __($attributes['label-name']);
    }
    if (!isset($attributes['required'])) {
        $attributes['required'] = false;
    }
    if (isset($slot)&&$slot!='') {
        $slot = $slot;
    }else{
        $slot = old($attributes['name']);
    }

@endphp
@if(!isset($attributes['disable-label']))
    <label for="{{$attributes['id']}}"
           style="float: {{app()->getLocale() == 'ar' ? 'right' : ''}}">{{__($attributes['label-name'])}} @if($attributes['required'])
            <span class="required text-danger">*</span>
        @endif</label>
@endif
<textarea {{$attributes->merge(['class'=>'form-control'])}}>{{$slot}}</textarea>
@error($attributes['name'])
<div class="text-danger">{{ $message }}</div>
@enderror
