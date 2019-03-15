<!doctype html>
<html>
   <head>
      <meta charset="utf-8">
      <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
      <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
      <title>{{ $service_instance->name }} API Documentation</title>
      <style>
         .highlight table td { padding: 5px; }
         .highlight table pre { margin: 0; }
         .highlight .gh {
         color: #999999;
         }
         .highlight .sr {
         color: #f6aa11;
         }
         .highlight .go {
         color: #888888;
         }
         .highlight .gp {
         color: #555555;
         }
         .highlight .gs {
         }
         .highlight .gu {
         color: #aaaaaa;
         }
         .highlight .nb {
         color: #f6aa11;
         }
         .highlight .cm {
         color: #75715e;
         }
         .highlight .cp {
         color: #75715e;
         }
         .highlight .c1 {
         color: #75715e;
         }
         .highlight .cs {
         color: #75715e;
         }
         .highlight .c, .highlight .cd {
         color: #75715e;
         }
         .highlight .err {
         color: #960050;
         }
         .highlight .gr {
         color: #960050;
         }
         .highlight .gt {
         color: #960050;
         }
         .highlight .gd {
         color: #49483e;
         }
         .highlight .gi {
         color: #49483e;
         }
         .highlight .ge {
         color: #49483e;
         }
         .highlight .kc {
         color: #66d9ef;
         }
         .highlight .kd {
         color: #66d9ef;
         }
         .highlight .kr {
         color: #66d9ef;
         }
         .highlight .no {
         color: #66d9ef;
         }
         .highlight .kt {
         color: #66d9ef;
         }
         .highlight .mf {
         color: #ae81ff;
         }
         .highlight .mh {
         color: #ae81ff;
         }
         .highlight .il {
         color: #ae81ff;
         }
         .highlight .mi {
         color: #ae81ff;
         }
         .highlight .mo {
         color: #ae81ff;
         }
         .highlight .m, .highlight .mb, .highlight .mx {
         color: #ae81ff;
         }
         .highlight .sc {
         color: #ae81ff;
         }
         .highlight .se {
         color: #ae81ff;
         }
         .highlight .ss {
         color: #ae81ff;
         }
         .highlight .sd {
         color: #e6db74;
         }
         .highlight .s2 {
         color: #e6db74;
         }
         .highlight .sb {
         color: #e6db74;
         }
         .highlight .sh {
         color: #e6db74;
         }
         .highlight .si {
         color: #e6db74;
         }
         .highlight .sx {
         color: #e6db74;
         }
         .highlight .s1 {
         color: #e6db74;
         }
         .highlight .s {
         color: #e6db74;
         }
         .highlight .na {
         color: #a6e22e;
         }
         .highlight .nc {
         color: #a6e22e;
         }
         .highlight .nd {
         color: #a6e22e;
         }
         .highlight .ne {
         color: #a6e22e;
         }
         .highlight .nf {
         color: #a6e22e;
         }
         .highlight .vc {
         color: #ffffff;
         }
         .highlight .nn {
         color: #ffffff;
         }
         .highlight .nl {
         color: #ffffff;
         }
         .highlight .ni {
         color: #ffffff;
         }
         .highlight .bp {
         color: #ffffff;
         }
         .highlight .vg {
         color: #ffffff;
         }
         .highlight .vi {
         color: #ffffff;
         }
         .highlight .nv {
         color: #ffffff;
         }
         .highlight .w {
         color: #ffffff;
         }
         .highlight {
         color: #ffffff;
         }
         .highlight .n, .highlight .py, .highlight .nx {
         color: #ffffff;
         }
         .highlight .ow {
         color: #f92672;
         }
         .highlight .nt {
         color: #f92672;
         }
         .highlight .k, .highlight .kv {
         color: #f92672;
         }
         .highlight .kn {
         color: #f92672;
         }
         .highlight .kp {
         color: #f92672;
         }
         .highlight .o {
         color: #f92672;
         }
      </style>
      <link href="/assets/css/fontawesome.5.7.2.css" rel="stylesheet" media="screen" />
      <link href="/assets/css/slate_screen.css" rel="stylesheet" media="screen" />
      <link href="/assets/css/slate_print.css" rel="stylesheet" media="print" />
      <script src="/assets/js/slate_all.js"></script>
   </head>
   <body class="index" data-languages="[&quot;shell&quot;,&quot;graphene&quot;]">
      <a href="#" id="nav-button">
      <span>
      NAV <i class="fa fa-hamburger fa-rotate-90"></i>
      <!-- <img src="/assets/images/navbar.png" alt="Navbar" /> -->
      </span>
      </a>
      <div class="toc-wrapper">
         <!-- <img src="images/logo.png" class="logo" alt="Logo" /> -->
         <div class="lang-selector">
            <a href="#" data-language-name="shell">shell</a>
            <a href="#" data-language-name="graphene">graphene</a>
         </div>
         <div class="search">
            <input type="text" class="search" id="input-search" placeholder="Search">
         </div>
         <ul class="search-results"></ul>
         <ul id="toc" class="toc-list-h1">
            <li>
               <a href="#introduction" class="toc-h1 toc-link" data-title="{{ $service_instance->name }} API">{{ $service_instance->name }} API</a>
            </li>
            <li>
               <a href="#resources" class="toc-h1 toc-link" data-title="Resources">Resources</a>
            </li>
            <li>
               <a href="#authentication" class="toc-h1 toc-link" data-title="Authentication">Authentication</a>
            </li>
            <li>
               <a href="#api-routes" class="toc-h1 toc-link" data-title="API Routes">API Routes</a>
               <ul class="toc-list-h2">
               @foreach ($service_version->routes as $si_key => $si_route)
               <?php if (!isset($si_route->params)) { $si_route->params = []; } ?>
                  <li>
                     <a href="#api-route-{{$si_key}}" class="toc-h2 toc-link" data-title="/{{$service_instance->slug}}{{$si_route->path}}">/{{$service_instance->slug}}{{$si_route->path}}</a>
                  </li>
                @endforeach
               </ul>
            </li>
            <li>
               <a href="#errors" class="toc-h1 toc-link" data-title="Errors">Errors</a>
            </li>
         </ul>
         <ul class="toc-footer">
            <li><a href='https://github.com/escherlabs/GrapheneAPIGateway'>Powered by GrapheneAPIGateway</a></li>
         </ul>
      </div>
      <div class="page-wrapper">
         <div class="dark-box"></div>
         <div class="content">
            <h1 id='introduction'><i>{{ $service_instance->name }}</i> API <span style="float:right;color:red;font-size:15px;">({{$service_instance->environment->type}})</span></h1>
            <p>Welcome to the <i>{{ $service_instance->name }}</i> API</p>
            <h3>Description</h3>
            <p>{{ $service_instance->service->description }}</p>
            <h3>Version Information</h3>
            <p>{{ $service_version->summary }}</p>
            <p>{{ $service_version->description }}</p>
            <table>
               <thead>
                  <tr>
                     <th>Saved / Published Date</th>
                     <th>Stable Flag</th>
                     <th>User</th>
                  </tr>
               </thead>
               <tbody>
                  <tr>
                     <td>{{ $service_version->updated_at }}</td>
                     <td>{{ $service_version->stable }}</td>
                     <td>{{ $service_version->user_id }}</td>
                  </tr>
               </tbody>
            </table>
<!-- Resources -->
            <h1 id='resources'>Resources</h1>
            @if(count($service_instance->resources) > 0)
            <p>These are the resources (databases, external APIs, etc) which are consumed by the <i>{{ $service_instance->name }}</i> API</p>
            <p>The table below can be used to identify the type of resources being used (mysql/oracle database), as well as the resource classification (dev/test/prod)</p>
<?php
$local_resources = [];
foreach($service_instance->resources as $si_resource_index => $si_resource) {
    foreach($resources as $resource) {
        if ( $si_resource->resource === $resource->id ) {
            $local_resources[$si_resource->name] = $resource;
        }
    }
}
?>
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Resource</th>
            <th>Type</th>
            <th>Dev/Test/Prod</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($local_resources as $local_resource_name => $local_resource)
        <tr>
            <td>{{$local_resource_name}}</td>
            <td>{{ $local_resource->name }}</td>
            <td>{{ $local_resource->resource_type }}</td>
            <td>{{ $local_resource->type }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@else
<p>This API has no resources.</p>
@endif

<!-- Authentication -->
            <h1 id='authentication'>Authentication</h1>
            <blockquote class="highlight graphene tab-graphene">
               <p>Create an Endpoint with the following configuration:</p>
            </blockquote>
            <pre class="highlight graphene tab-graphene">
            <code>
Auth Type: HTTP Basic Auth
URL: {{ url($service_instance->slug) }}
Username: username
Password: password
            </code>
            </pre>
            <blockquote class="highlight shell tab-shell">
               <p>You can authenticate against this API with the following:</p>
            </blockquote>
            <pre class="highlight shell tab-shell">
            <code>
curl {{ url($service_instance->slug) }}
    -u username:password
            </code>
            </pre>
            <blockquote class="highlight">
               <p>Make sure to replace <code>username</code> and <code>password</code> with your API credentials.</p>
            </blockquote>
            <p>The <i>{{ $service_instance->name }}</i> API uses HTTP basic authentication to allow access to the API.  Contact the API Developer @isset($service_instance->service->user)({{ $service_instance->service->user }}) @endisset to generate a username/password to use this API</a>.</p>
            <aside class="success">There is/are currently {{count($service_instance->route_user_map)}} user account(s) / app username(s) for the <i>{{ $service_instance->name }}</i> API:
                <ul>
                    @foreach($users as $user)
                    <li>{{ $user->app_name }}</li>
                    @endforeach
                </ul>
            </aside>
<!-- End Authentication -->
<!-- API --> 
            <h1 id='api-routes'>API Routes</h1>
@foreach ($service_version->routes as $si_key => $si_route)
<?php if (!isset($si_route->params)) { $si_route->params = []; } ?>
<!-- First One -->

            <h2 id='api-route-{{$si_key}}'>/{{$service_instance->slug}}{{$si_route->path}}</h2>
<pre class="highlight graphene tab-graphene">
<b><u>Endpoint Definition</u></b>
Auth Type: HTTP Basic Auth
URL: {{ url($service_instance->slug) }}
Username: username
Password: password

<b><u>MicroApp</u></b>
Resource Path: {{ $si_route->path }}
<?php if ($si_route->verb === 'ALL') {
    echo "Resource Fetch: true or false (configurable)\n";
    echo "JavaScript Methods: this.app.get, this.app.post, this.app.put, this.app.delete\n";

} else if ($si_route->verb === 'GET') {
    echo "Resource Fetch: true or false (configurable)\n";
    echo "JavaScript Method: this.app.".strtolower($si_route->verb)."\n";

} else {
    echo "Resource Fetch: false (Can't perform fetch on non GET API!)\n";
    echo "JavaScript Method: this.app.".strtolower($si_route->verb)."\n";
}?>
</pre>
@if ($si_route->verb === 'ALL')
<pre class="highlight shell tab-shell">
<code>
curl {{ url($service_instance->slug.$si_route->path) }}?@foreach ($si_route->params as $param_index => $param){{$param->name}}=var{{$param_index}}&@endforeach

  -u username:password
</code>
<code>
curl {{ url($service_instance->slug.$si_route->path) }}
  -X POST
  -d "@foreach ($si_route->params as $param_index => $param){{$param->name}}=var{{$param_index}}&@endforeach"
  -u username:password
</code>
<code>
curl {{ url($service_instance->slug.$si_route->path) }}
  -X PUT
  -d "@foreach ($si_route->params as $param_index => $param){{$param->name}}=var{{$param_index}}&@endforeach"
  -u username:password
</code>
<code>
curl {{ url($service_instance->slug.$si_route->path) }}
  -X DELETE
  -d "@foreach ($si_route->params as $param_index => $param){{$param->name}}=var{{$param_index}}&@endforeach"
  -u username:password
</code>
</pre>
@else
@if ($si_route->verb === 'GET')
<pre class="highlight shell tab-shell">
<code>
curl {{ url($service_instance->slug.$si_route->path) }}?@foreach ($si_route->params as $param_index => $param){{$param->name}}=var{{$param_index}}&@endforeach

  -u username:password
</code>
</pre>
@else 
<pre class="highlight shell tab-shell">
<code>
curl {{ url($service_instance->slug.$si_route->path) }}
  -X {{$si_route->verb}}
  -d "@foreach ($si_route->params as $param_index => $param){{$param->name}}=var{{$param_index}}&@endforeach"
  -u username:password
</code>
</pre>
@endif
@endif  
<blockquote>
    <p>Make sure to replace <code>username</code> and <code>password</code> with your API credentials</p>
    @if (count($si_route->params)>0)
</blockquote>
<blockquote class="highlight shell tab-shell">
    <p>Make sure to replace <code>var0</code> through <code>varN</code> with the relevant values.</p>
    @endif
</blockquote>

            <p>{{$si_route->description}}</p>
            @if ($si_route->verb === 'ALL')
                <h3 id='http-request'>HTTP Request</h3>
                <p><code>GET {{ url($service_instance->slug.$si_route->path) }}<?php foreach($si_route->params as $param) { if ($param->required === true || $param->required === "true") { echo "/&lt;".$param->name."&gt;"; } }?></code></p>
                <p><code>POST {{ url($service_instance->slug.$si_route->path) }}<?php foreach($si_route->params as $param) { if ($param->required === true || $param->required === "true") { echo "/&lt;".$param->name."&gt;"; } }?></code></p>
                <p><code>PUT {{ url($service_instance->slug.$si_route->path) }}<?php foreach($si_route->params as $param) { if ($param->required === true || $param->required === "true") { echo "/&lt;".$param->name."&gt;"; } }?></code></p>
                <p><code>DELETE {{ url($service_instance->slug.$si_route->path) }}<?php foreach($si_route->params as $param) { if ($param->required === true || $param->required === "true") { echo "/&lt;".$param->name."&gt;"; } }?></code></p>
                <h3>Parameters</h3>
                @if (count($si_route->params)>0)
                    <aside class="note">Required parameters can be sent as x-www-form-urlencoded variables, query string variables (example: <code>?{{$si_route->params[0]->name}}=pizza)</code> or as part of the directory path (example: <code>/pizza</code>)</aside>
                @endif
                <aside class="note">Optional parameters can be sent as x-www-form-urlencoded variables or query string variables (example: <code>?tacos=good</code>)</aside>
            @else
                <h3 id='http-request'>HTTP {{$si_route->verb}} Request</h3>
                <p><code>{{$si_route->verb}} {{ url($service_instance->slug.$si_route->path) }}<?php foreach($si_route->params as $param) { if ($param->required === true || $param->required === "true") { echo "/&lt;".$param->name."&gt;"; } }?></code></p>
                <h3>Parameters</h3>
                @if ($si_route->verb === 'GET')
                    @if (count($si_route->params)>0)
                        <aside class="note">Required parameters can be sent as either query string variables (example: <code>?{{$si_route->params[0]->name}}=pizza)</code> or as part of the directory path (example: <code>/pizza</code>)</aside>
                    @endif
                    <aside class="note">Optional parameters must be sent as  query string variables (example: <code>?tacos=good</code>)</aside>
                @else
                    @if (count($si_route->params)>0)
                        <aside class="note">Required parameters can be sent as x-www-form-urlencoded {{$si_route->verb}} variables, query string variables (example: <code>?{{$si_route->params[0]->name}}=pizza)</code> or as part of the directory path (example: <code>/pizza</code>)</aside>
                    @endif
                    <aside class="note">Optional parameters can be sent as x-www-form-urlencoded {{$si_route->verb}} variables or query string variables (example: <code>?tacos=good</code>)</aside>
                @endif
            @endif

            @if (count($si_route->params)>0)
            <table>
               <thead>
                  <tr>
                     <th>Parameter</th>
                     <th>Required</th>
                  </tr>
               </thead>
               <tbody>
                @foreach ($si_route->params as $param)
                  <tr>
                     <td>{{$param->name}}</td>
                     <td>
                     @if ($param->required === true || $param->required === "true")
                        true
                     @else
                        false
                     @endif
                     </td>
                  </tr>
                @endforeach
               </tbody>
            </table>
            <aside class="warning">There may be additional unlisted optional parameters</aside>
            @else 
                <aside class="warning">There are no optional parameters listed (there may be unlisted optional parameters)</aside>
            @endif
@endforeach
<!-- Errors -->
            <h1 id='errors'>Errors</h1>
            <p>The <i>{{ $service_instance->name }}</i> API uses the following HTTP error codes:</p>
            <table>
               <thead>
                  <tr>
                     <th>Error Code</th>
                     <th>Meaning</th>
                  </tr>
               </thead>
               <tbody>
                  <tr>
                     <td>400</td>
                     <td>Bad Request -- Your request is invalid.</td>
                  </tr>
                  <tr>
                     <td>401</td>
                     <td>Unauthorized -- Your API credentials are wrong.</td>
                  </tr>
                  <tr>
                     <td>404</td>
                     <td>Not Found -- The specified API could not be found.</td>
                  </tr>
                  <tr>
                     <td>405</td>
                     <td>Method Not Allowed -- You tried to access an API with an invalid method.</td>
                  </tr>
                  <tr>
                     <td>500</td>
                     <td>Internal Server Error -- We had a problem with our server. Try again later.</td>
                  </tr>
                  <tr>
                     <td>503</td>
                     <td>Service Unavailable -- We&#39;re temporarily offline for maintenance. Please try again later.</td>
                  </tr>
               </tbody>
            </table>
         </div>
         <div class="dark-box">
            <div class="lang-selector">
               <a href="#" data-language-name="graphene">Graphene MicroApps</a>
               <a href="#" data-language-name="shell">Shell / curl</a>
            </div>
         </div>
      </div>
   </body>
</html>