@php
    use App\Modul;
    use App\SubModul;
@endphp
<!-- ========== Left Sidebar Start ========== -->
<div class="left side-menu">
    <div class="sidebar-inner slimscrollleft">

        <!-- User -->
        <div class="user-box">
            <div class="user-img">
                <img src="{{ asset('assets/images/users/alvin.jpg') }}" alt="user-img" title="Mat Helme" class="rounded-circle img-thumbnail img-responsive">
                <div class="user-status offline"><i class="mdi mdi-adjust"></i></div>
            </div>
            <h5><a href="#">Superadmin</a> </h5>
            <ul class="list-inline">
                <li class="list-inline-item">
                    <a href="#" >
                        <i class="mdi mdi-settings"></i>
                    </a>
                </li>

                <li class="list-inline-item">
                    <a href="#" class="text-custom">
                        <i class="mdi mdi-power"></i>
                    </a>
                </li>
            </ul>
        </div>
        <!-- End User -->

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <ul>
                @foreach (Modul::getAllModul() as $modul)
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="{{$modul->modul_icon}}"></i><span>{{$modul->modul_desc}}</span> <span class="menu-arrow"></span></a>
                            <ul class="list-unstyled">
                                @foreach (SubModul::getSub($modul->modul_id) as $sub)
                                    <li><a href="/{{$sub->submodul_page}}">{{$sub->submodul_desc}}</a></li>
                                @endforeach
                            </ul>
                    </li>
                @endforeach
            </ul>
            <div class="clearfix"></div>
        </div>
        <!-- Sidebar -->
        <div class="clearfix"></div>

    </div>

</div>
<!-- Left Sidebar End -->