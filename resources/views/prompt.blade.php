Correct the email address from the provided text, return only the domain:

Input: example@yaohh.com
@if(!config('ai-email-suggest.use_chatgpt_api')) Email:  @endif yahoo.com

Input:other@gmial.com
@if(!config('ai-email-suggest.use_chatgpt_api')) Email:  @endif gmail.com

Input:{{$email}}
@if(!config('ai-email-suggest.use_chatgpt_api')) Email:  @endif
