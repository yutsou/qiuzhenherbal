@extends('layouts.general_member')

@section('content')
    <div class="uk-margin">
        <div class="uk-width-1-1">
            <a href="/" class="custom-link">首頁</a>
            >
            <a href="{{ url()->current() }}" class="custom-link">{{ $head }}</a>
        </div>
    </div>
    <div class="uk-width-1-1@s">
        <div class="uk-card uk-card-default uk-card-body">
            <div class="uk-margin-medium">
                <h1 class="uk-card-title">{{ $head }}</h1>
            </div>
            <p>
                歡迎您來到求真草本 Qiuzhen&Herbal 的網站。
                <br>
                詐騙集團屢屢猖獗，為讓您能夠安心使用本網站購物，請您詳閱下列內容：
                <br>
                <br>若您接獲通知您下單 求真草本 訂單有：<strong>「信用卡誤簽分期」、「款項錯誤」、「更改結帳方式」、「超商條碼異常」、「訂購數量錯誤」</strong>等名義，需請您配合進行<strong>「ATM自動櫃員機操作」、「補繳金額」、「變更付款方式」、「提供刷卡資訊」</strong>等，皆是現在流行的詐騙手法，請小心勿受騙上當，切記提高警覺冷靜查詢，並向求真草本客服或165反詐騙電話查證，以確保安全。
            </p>
            <ul class="uk-list">
                <li>
                    【165反詐騙諮詢專線 】 手機/市話直撥：165
                </li>
                <li>
                    【求真草本 客服電話 】 客服電話：08-7796578，服務時間：週一至週五 9:00 – 17:00 (週六/日及國定假日休息)
                </li>
                <li>
                    【求真草本 客服信箱 】 service@qiuzhenherbal.com.tw
                </li>
                <li>
                    【求真草本 FaceBook 粉絲專業 】 『求真草本』
                </li>
            </ul>
            <p>
                <strong>提醒您：</strong>
                <br>求真草本 Qiuzhen&Herbal 不會以任何理由要求客戶操作ATM變更訂單付款設定，如接獲疑似詐騙電話，請不要依指示操作，若有任何疑問，請直接與客服聯絡。
            </p>
        </div>
    </div>
@endsection
