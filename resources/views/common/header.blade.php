<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="theme-color" content="#000000" />
<meta name="description" content=""/>
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="random-key" content="{{ generateRandomCode(32) }}">
<link rel="stylesheet" href="{{app_url()}}/assets/vendors/line-awesome/css/line-awesome.min.css"/> 
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"
/>

<link rel="stylesheet" href="{{app_url()}}/assets/css/styles.min.css"/> 

<title>{{ app_name() }}</title>