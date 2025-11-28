<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>白石高爾夫球練習場 打球位置 預訂表格 - 
White Head Club - Booking Form for Golf Court</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
<link rel="stylesheet" href="./style.css">


<style type="text/css">


body {
  display: -webkit-box;
  display: -moz-box;
  display: box;
  display: -webkit-flex;
  display: -moz-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: center;
  -moz-box-pack: center;
  box-pack: center;
  -webkit-justify-content: center;
  -moz-justify-content: center;
  -ms-justify-content: center;
  -o-justify-content: center;
  justify-content: center;
  -ms-flex-pack: center;
  -webkit-box-align: center;
  -moz-box-align: center;
  box-align: center;
  -webkit-align-items: center;
  -moz-align-items: center;
  -ms-align-items: center;
  -o-align-items: center;
  align-items: center;
  -ms-flex-align: center;
}

* {
  box-sizing: border-box;
}

html, body {
/*  background-color: #e8eaf6;*/
  font-family: "Montserrat", "Lucida Grande", "Lucida", helvetica, arial, sans-serif;
  font-weight: 200;
  background-image: url("4c2dd3a4-9b6f-4a23-bb55-f2d0ab71397a.jpg");
}

.payment {
  
  background-color: #3949ab;
  width: 30rem;
  border: 1px solid #303f9f;
/*  box-shadow: 0px 10px 100px #d1c4e9;*/
}

.payment__headline {
  padding: 1rem;
  font-size: 1.5em;
  color: #ede7f6;
}

.payment__group {
  position: relative;
}

.payment__label {
  display: block;
  width: 100%;
  padding-left: 1rem;
  position: absolute;
  z-index: 10;
  pointer-events: none;
  top: 1.5rem;
  font-size: 1rem;
  color: #b39ddb;
  -webkit-transition: all, 0.25s;
  -moz-transition: all, 0.25s;
  transition: all, 0.25s;
}

.payment__field:focus ~ label, .payment__field--filled ~ label {
  top: 0.5rem;
  font-size: 0.6em;
  color: #5e35b1;
}

.payment__field {
  display: block;
  width: 100%;
  position: relative;
  padding: 1.25rem 1rem 1rem;
  margin-bottom: 0;
  border: 0;
  font-size: 1.5rem;
  color: #4527a0;
  border-bottom: 1px solid #4527a0;
}
.payment__field:focus {
  outline: 0;
}

.payment__field--name {
  border-top: 1px solid #4527a0;
}

.columns {
  width: 100%;
  display: block;
  position: relative;
}

.payment__group--date, .payment__group--cvv {
  width: 50%;
  float: left;
  display: inline-block;
}

.payment__field--date {
  border-right: 1px solid #4527a0;
}

.payment__tooltip {
  display: none;
}

.payment__footer {
  display: -webkit-box;
  display: -moz-box;
  display: box;
  display: -webkit-flex;
  display: -moz-flex;
  display: -ms-flexbox;
  display: flex;
  width: 100%;
}

.payment__location, payment__next a {
  padding: 1rem;
}

.payment__location {
  background-color: #212121;
  color: #e0e0e0;
  position: relative;
  width: 15%;
  text-align: center;
}

.payment__next {
  width: 85%;
}

.payment__next a {
  background-color: #1e88e5;
  color: #e3f2fd;
  width: 100%;
  display: block;
  padding: 1rem;
  text-align: center;
  text-decoration: none;
  -webkit-transition: all 0.25s;
  -moz-transition: all 0.25s;
  transition: all 0.25s;
}
.payment__next a:hover, .payment__next a:focus {
  outline: 0;
  background-color: #1565c0;
  padding-left: 2rem;
}

strong {
  font-weight: bold;
}

</style>

</head>
<body>
<!-- partial:index.partial.html -->
<div class='payment'>
  <h1 class='payment__headline'>白石高爾夫球練習場.<br>
打球位置 預訂表格<br>
White Head Club<br>
Booking Form for Golf Court</h1>
  <form class='payment__form'>
    <div class='payment__group payment__group--name'>
      <input class='payment__field payment__field--name'>
      <label class='payment__label payment__label--name'>姓名 Full Name</label>
    </div>
    <div class='payment__group payment__group--creditcard'>
      <input class='payment__field payment__field--creditcard'>
      <label class='payment__label payment__label--creditcard'>電子郵件地址 Email Address</label>
    </div>
    <div class='payment__group payment__group--name'>
      <input class='payment__field payment__field--name'>
      <label class='payment__label payment__label--name'>電話號碼 Telephone No.</label>
    </div>
    <div class='payment__group payment__group--name'>
      <input class='payment__field payment__field--name'>
      <label class='payment__label payment__label--name'>八達通號碼
Octopus No.</label>
    </div>

<hr>

    <div class='payment__group payment__group--name'>
      <input class='payment__field payment__field--name'>
      <label class='payment__label payment__label--name'>預訂日期 Reservation date</label>
    </div>
    <div class='payment__group payment__group--name'>
      <input class='payment__field payment__field--name'>
      <label class='payment__label payment__label--name'>開始時間 (小時)Starting time (Hour)</label>
    </div>
    <div class='payment__group payment__group--name'>
      <input class='payment__field payment__field--name'>
      <label class='payment__label payment__label--name'>結尾時間 (小時) End time (Hours)</label>
    </div>

<hr>

<h3 class='payment__headline'>
請選擇高爾夫打球 預訂位置<br>
Please select a golf course to reserve your spot
</h3>




    <div class='columns'>
      <div class='payment__group payment__group--date'>
        <input class='payment__field payment__field--date'>
        <label class='payment__label payment__label--date'>電話號碼 Telephone No.</label>
      </div>
      <div class='payment__group payment__group--cvv'>
        <input class='payment__field payment__field--cvv'>
        <label class='payment__label payment__label--cvv'>CVV <span class="payment__tooltip payment__tooltip--CVV">What's this?</span></label>
      </div>
    </div>
  </form>
  <div class='payment__footer'>
    <div class='payment__location'>
      1/5
    </div>
    <div class='payment__next'>
      <a href="#"><strong>Next</strong>: Billing Address <span class="fa fa-arrow-right"></span></a>
    </div>
  </div>
</div>
<!-- partial -->
  <script src='//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script><script  src="./script.js"></script>

</body>
</html>
