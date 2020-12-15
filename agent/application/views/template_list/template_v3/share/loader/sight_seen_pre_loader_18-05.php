<style>
      /* NOTE: The styles were added inline because Prefixfree needs access to your styles and they must be inlined if they are on local disk! */
      html{
  font-size:62.5%;
}
body{
  background: #f0f0e1;
  font:300 1.6em/1.4em Helvetica, Arial, "sans-serif";
}
h1{padding:1.2em; text-align:center;}
.scene{position:relative;
  display:block; 
  margin:0 auto; 
  width:300px;
  height:200px; 
}
.plane,
.cloud{
  position:absolute;
}
/*plane animation*/
.plane{ 

  animation-name: anim-plane;
  animation-iteration-count: infinite;
  animation-direction: alternate;
  animation-timing-function:linear;
  
  animation-fill-mode:forwards;	   
  display:block;
  margin:0 auto;
  transform: translateY(80px);
  left:30%;
      margin: 10px 0;
}

@keyframes anim-plane{ 
  to{
    transform:translateY(95px);
  }    
}


/* Cloud Animation */

@keyframes fade{
  0%{ opacity: 0;}
  10%{ opacity: 1;}
  90%{ opacity:1;}
  100%{ opacity:0;}
}

@keyframes move{  
  from{ 
    left:200px; 
  }
  to{ 
    left:0px; 
  }
}
 

.cloud{ 
  animation-duration:10s; 
  animation-name:move, fade;
  animation-direction: normal;
  animation-iteration-count:infinite;
  animation-timing-function:linear; 
  animation-fill-mode:both;	  
  
  display:block;
  background:url(data:image/svg+xml;base64,PHN2ZyBpZD0iY2xvdWQiIHZpZXdCb3g9IjAgMCA1MiA0MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiAgeD0iMHB4IiB5PSIwcHgiIHdpZHRoPSI1MnB4IiBoZWlnaHQ9IjQwcHgiPgoJPGRlZnM+CgkJPGZpbHRlciBpZD0iZjEiIHg9Ii0xMDAlIiB5PSItMTAwJSIgd2lkdGg9IjMwMCUiIGhlaWdodD0iMzAwJSI+IAoJCQk8ZmVPZmZzZXQgcmVzdWx0PSJvdXQiIGluPSJTb3VyY2VHcmFwaGljIiBkeD0iMCIgZHk9IjEiLz4KCQkJPGZlQ29sb3JNYXRyaXggcmVzdWx0PSJvdXQiIGluPSJvdXQiIHR5cGU9Im1hdHJpeCIgdmFsdWVzPSIwIDAgMCAwIDAgIDAgMCAwIDAgMCAgMCAwIDAgMCAwICAwIDAgMCAwLjQgMCIvPgoJCQk8ZmVHYXVzc2lhbkJsdXIgcmVzdWx0PSJvdXQiIGluPSJvdXQiIHN0ZERldmlhdGlvbj0iMiIvPgoJCQk8ZmVCbGVuZCBpbj0iU291cmNlR3JhcGhpYyIgaW4yPSJvdXQiIG1vZGU9Im5vcm1hbCIgcmVzdWx0PSJkcCIvPgoJCTwvZmlsdGVyPgoJPC9kZWZzPiAKCTxwYXRoIGlkPSJmZy1jbG91ZCIgZmlsdGVyPSJ1cmwoI2YxKSIgZD0iTTYuMyAzNS4xQzQuNyAzNC4yLTAuNCAzMi4zIDEuNCAyNSAzLjEgMTguMSA4LjcgMTkuNSA4LjcgMTkuNSA4LjcgMTkuNSAzLjIgMTQuMSAxMC40IDYuOCAxNi45IDAuMiAyMy4xIDQuNiAyMy4xIDQuNiAyMy4xIDQuNiAzMC0xLjcgMzUuMiAyLjQgNDQuNiA5LjcgNDIuOCAyNS4zIDQyLjggMjUuMyA0Mi44IDI1LjMgNDggMjIuNiA0OS44IDI4LjYgNTEgMzIuNyA0NiAzNS44IDQyLjggMzYuNyAzOS43IDM3LjUgOC45IDM2LjYgNi4zIDM1LjFaIiBzdHJva2U9IiNjY2NjY2MiIHN0cm9rZS13aWR0aD0iMSIgZmlsbD0iI2ZmZmZmZiIvPgo8L3N2Zz4=);
  height:40px;
  width:53px;
  margin:0 auto;  
}
.cloud--small{
  animation-duration:6s; 
  top:65px;
  transform: scaleX(0.5) scaleY(0.5); 
}
.cloud--medium{ 
  animation-duration:5s;
  animation-delay:1s;
  top:95px;
  transform: scaleX(0.7) scaleY(0.7); 
}
.cloud--large{
  animation-duration:4.5s;
  animation-delay:2.5s;
  top:95px;
  transform: scaleX(0.8) scaleY(0.8); 
}

.whoami{
  padding-top:3em;
  text-align:center;  
}
    </style>
<div class="fulloading result-pre-loader-wrapper forhoteload">
	<div class="loadmask"></div>
    <div class="centerload cityload">
    
    	<div class="loadcity hide"></div>
    
    	<div class="clodnsun hide"></div>
        
        <div class="reltivefligtgo hide">
        	<div class="flitfly hide"></div>
        </div>
        <div class="scene">
  <span class="cloud cloud--small"></span>
  <svg xmlns="http://www.w3.org/2000/svg" id="plane" class="plane" viewBox="0 0 104 47" x="0" y="0" width="104" height="100" background-color="#ffffff00">
    <g id="avion">
      <path fill="#F8A805" d="M50.217,10.233c0,2.478-2.03,4.487-4.535,4.487c-2.505,0-4.535-2.009-4.535-4.487s2.03-4.487,4.535-4.487
  C48.187,5.746,50.217,7.755,50.217,10.233z M46.638,2h-1.914v2.675h1.914V2z M44.724,18.456h1.914v-2.675h-1.914V18.456z
   M39.125,15.376l1.352,1.339l1.912-1.891l-1.353-1.339L39.125,15.376z M52.24,5.079L50.886,3.74l-1.911,1.891l1.354,1.339
  L52.24,5.079z M48.971,14.825l1.911,1.892l1.355-1.339l-1.912-1.891L48.971,14.825z M42.388,5.633l-1.912-1.891L39.124,5.08
  l1.91,1.892L42.388,5.633z M51.296,9.282v1.894h2.703V9.282H51.296z M40.068,9.282h-2.703v1.894h2.703V9.282z M8.415,31.186
  c0.145,0.368,0.502,0.602,0.891,0.602c0.049,0,0.1-0.004,0.148-0.011c0.444-0.069,0.78-0.436,0.808-0.879
  c0.002-0.031,0.214-3.12,2.133-4.024c0.477-0.225,0.68-0.79,0.453-1.262c-0.227-0.473-0.798-0.672-1.275-0.448
  c-1.65,0.777-2.459,2.394-2.854,3.697c-1.61-0.905-3.818-0.763-4.152-0.735c-0.527,0.044-0.919,0.502-0.874,1.023
  c0.044,0.521,0.508,0.9,1.034,0.864C5.386,29.959,7.947,29.996,8.415,31.186z M16.757,36.801c-1.741-0.557-3.451,0.071-4.641,0.751
  c-0.545-1.752-2.249-3.146-2.51-3.354c-0.412-0.326-1.013-0.258-1.343,0.15C7.933,34.755,8,35.351,8.412,35.677
  c0.636,0.504,2.344,2.203,1.87,3.358c-0.17,0.413-0.025,0.887,0.347,1.138c0.164,0.111,0.352,0.165,0.539,0.165
  c0.238,0,0.475-0.088,0.659-0.257c0.023-0.021,2.338-2.119,4.342-1.478c0.503,0.16,1.043-0.113,1.205-0.611
  C17.536,37.495,17.26,36.961,16.757,36.801z"/>
<path fill="#000" d="M8.316,50.003v2.525H3.269v-2.525H8.316z M21.768,50.003H10.283v2.525h11.485V50.003z M28.362,50.003H23.47v2.525h4.892
  V50.003z M37.106,50.003h-5.66v2.525h5.66V50.003z M39.146,52.528h13.073v-2.525H39.146V52.528z M47.171,56h5.048v-2.525h-5.048V56z
   M33.719,56h11.485v-2.525H33.719V56z M27.126,56h4.892v-2.525h-4.892V56z M18.381,56h5.661v-2.525h-5.661V56z M3.269,56h13.072
  v-2.525H3.269V56z M20.2,6.853c-4.69,0-8.975,1.692-12.287,4.481h24.574C29.175,8.545,24.89,6.853,20.2,6.853z M34.43,13.228H5.969
  c-0.708,0.795-1.349,1.646-1.915,2.551h32.291C35.78,14.873,35.138,14.022,34.43,13.228z M52.219,45.979v2.525H3.269v-2.525h15.578
  V20.291H2c0.27-0.904,0.614-1.776,1.012-2.617h34.375c0.4,0.841,0.743,1.713,1.014,2.617H21.4V45.98h4.175l2.783-2.5
  c-1.265-0.3-2.584-0.698-3.961-1.207l0.892-2.366c1.946,0.718,3.749,1.206,5.411,1.469l17.439-15.662l1.716,1.87l-3.29,2.956
  c-0.1,1.465-0.588,5.442-2.98,8.733l4.497,6.706H52.219z M41.05,35.491l0.996,1.486c0.737-1.243,1.203-2.564,1.495-3.723
  L41.05,35.491z M34.267,41.583c1.938-0.115,3.609-0.642,5.001-1.585c0.41-0.278,0.783-0.586,1.127-0.915l-1.257-1.874L34.267,41.583
  z M45.02,45.978l-3.195-4.764c-0.341,0.3-0.705,0.587-1.098,0.855c-2.022,1.375-4.453,2.063-7.271,2.063
  c-0.619,0-1.258-0.038-1.913-0.103l-2.171,1.949H45.02z"/>

    </g>
  </svg>
  <span class="cloud cloud--medium"></span>
  <span class="cloud cloud--large"></span>
</div>  
        
        <div class="relativetop">
            <div class="paraload">
                Searching for the best SightSeeing Places
            </div>
            <div class="clearfix"></div>
            <div class="placenametohtl"><?php echo ucfirst($sight_seen_search_params['destination']); ?></div>
            <div class="clearfix"></div>
            <div class="sckintload hide">
                <div class="ffty">
                    <div class="borddo brdrit">
                    <span class="lblbk">From Date</span>
                    </div>
                </div>
                
                <div class="ffty">
                    <div class="borddo">
                    <span class="lblbk">To Date</span>
                    </div>
                </div>
                
                <div class="clearfix"></div>
                
                <div class="tabledates">
                <div class="tablecelfty">
                    <div class="borddo brdrit">
                     <?php if($sight_seen_search_params['from_date']):?>
                        <div class="fuldate">
                        
                            <span class="bigdate"><?php echo date("d",strtotime($sight_seen_search_params['from_date']));?></span>
                            <div class="biginre">
                                <?php echo date("M",strtotime($sight_seen_search_params['from_date']));?><br />
    							<?php echo date("Y",strtotime($sight_seen_search_params['from_date']));?>
                            </div>
                        </div>
                    <?php endif;?>
                    </div>
                </div>

                <div class="tablecelfty">
                    <div class="borddo">
                    <?php if($sight_seen_search_params['to_date']):?>
                        <div class="fuldate">
                            <span class="bigdate"><?php echo date("d",strtotime($sight_seen_search_params['to_date']));?></span>
                            <div class="biginre">
                                 <?php echo date("M",strtotime($sight_seen_search_params['to_date']));?><br />
    							 <?php echo date("Y",strtotime($sight_seen_search_params['to_date']));?>
                            </div>
                        </div>
                    <?php endif;?>
                    </div>
                </div>
                </div>
                
                <div class="clearfix"></div>
                
               
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="busrunning hide">
            <div class="runbus hide"></div>
            <div class="runbus2 hide"></div>
            <div class="roadd hide"></div>
        </div>
    </div>
</div>