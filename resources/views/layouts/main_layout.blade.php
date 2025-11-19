<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title> 
    @vite('resources/css/app.css')    <!--  css  -->
    @vite('resources/js/app.js')      <!--  importação de bibliotecas, js externo e frameworks front end  -->


</head>
<body>
@include('layouts.top_bar')

@yield('content')

</body>
