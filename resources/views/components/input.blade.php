@php
    if (!isset($attributes['id'])) {
        $attributes['id'] = $attributes['name'];
    }

    if (!isset($attributes['label-name']) &&!isset($attributes['disable-label'])) {
        $attributes['label-name'] = ucwords(str_replace(['_id', '[]'], '', str_replace('_', ' ', $attributes['name'])));
    }else{
        $attributes['label-name'] = ucwords($attributes['label-name']);
    }
    if (!isset($attributes['type'])) {
        $attributes['type'] = 'text';
    }
    if (!isset($attributes['placeholder']) && !isset($attributes['disable-label'])) {
        $attributes['placeholder'] = __($attributes['label-name']);
    }
    if (!isset($attributes['required'])) {
        $attributes['required'] = false;
    }
    if (!isset($attributes['class'])) {
        $attributes['class'] = 'form-control';
    }
    if (isset($slot)&&$slot=='') {
        $slot=old($attributes['name']);
    }

@endphp

@if(!isset($attributes['disable-label']))
    <label for="{{$attributes['id']}}"
           style="float: {{app()->getLocale() == 'ar' ? 'right' : ''}}">{{__($attributes['label-name'])}} @if($attributes['required'])
            <span class="required text-danger">*</span>
        @endif</label>
@endif
<input {{$attributes->merge(['value'=>$slot])}} >
@error($attributes['name'])
<div class="text-danger">{{ $message }}</div>
@enderror



