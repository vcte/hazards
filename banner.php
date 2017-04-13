<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<div id="header">
				<div id="banner">
					<div id="logo">
						<div id="logo-image">
							<a title="University of Illinois" alt="University of Illinois" href="http://illinois.edu">
								<img border="0" src="static/imark50-dark.png"/>
							</a>
						</div>
						
						<div id="logo-text">
							<a title="Engineering at Illinois: Home" alt="Home" href="http://engineering.illinois.edu"><span class="logo-bold">Engineering</span> At Illinois</a>
						</div>
					</div>
				</div><!--close banner-->

				<div id="mainnav"> 
					<ul id="nav">
						<li><a href="/">My.ENGR</a>
							<ul>
								<li><a href="/myinfo">My Info</a></li>
								<li><a href="/chpwd.asp">Change Password</a></li>
								<li><a href="/editprefs.asp">Change Preferences</a></li>
								<li><a href="/refresh.asp">Refresh</a></li>
								<li><a href="/toggleprint.asp">Print View</a></li>
								<li><A HREF="/logout.asp" CLASS="banner3link">Sign Out</A></li>
							</ul>
						</li>
					
						<li><a href="/">Shared Services</a>
							<ul>
								<li><a href="http://business.engr.illinois.edu">Business</a></li>
								<li><a href="http://hr.engr.illinois.edu">Human Resources</a></li>
								<li><a href="http://it.engineering.illinois.edu">Information Technology</a></li>
							</ul>
						</li>
						
						<li><a href="/">HR Apps</a>
							<ul>
								<li><a href="/appointments">Appointments</a></li>
								<li><a href="/biodata">Biodata</a></li>
								<li><a href="/directory">Directory</a></li>
								<li><a href="/review">Review System</a></li>
								<li><a href="/vacation">Vacation/Sick Leave Reporting</a></li>
								<li><a href="/timetracker">TimeTracker</a></li>
							</ul>
						</li>
						
						<li><a href="/">Finance Apps</a>
							<ul>
								<li><a href="/chart">Chart</a></li>
								<li><a href="/costshare">Cost Share</a></li>
								<li><a href="/grants">Grants</a></li>
								<li><a href="/icr">ICR</a></li>
								<li><a href="/cybercash">I-Pay</a></li>
								<li><a href="/purchasing">Purchasing</a></li>
								<li><a href="/statements">Statements</a></li>
							</ul>
						</li>
						
						<li><a href="/">Facilities Apps</a>
							<ul>
								<li><a href="/space">Facilities</a></li>
								<li><a href="/keys">Keys</a></li>
								<li><a href="/people">People</a></li>
								<li><a href="/inventory">Inventory</a></li>
								<li><a href="/plasma">Digital Signs</a></li>
							</ul>
						</li>
						
						<li><a href="/">Academic Apps</a>
							<ul>
								<li><a href="/fair/">Academic Integrity (FAIR)</a></li>
								<li><a href="/coursechanges">Add/Drop Classes</a></li>
								<li><a href="/advising/">Advising Appointments</a></li>
								<li><a href="/classplanning/">Class Planning</a></li>
								<li><a href="/gradapps">Grad Applications</a></li>
								<li><a href="/gradrecs">Grad Records</a></li>
								<li><a href="/james-scholar">James Scholar</a></li>
								<li><a href="/rosters">Rosters</a></li>
								<li><a href="/smd">Secure Message Delivery</a></li>
								<li><a href="/studyabroadcourses/">Study Abroad Courses</a></li>
								<li><a href="/ugradrecs">Undergrad Records</a></li>
							</ul>
						</li>
						
						<li><a href="/">Administrative Apps</a>
							<ul>
								<li><a href="/attendance/">Attendance Tracking</a></li>
								<li><a href="/ecs/">ECS Tools</a></li>
								<li><a href="/ecs/interviews/">ECS Interview Signup</a></li>
								<li><a href="/eventreg/submissions/">Event Registration Submissions</a></li>
								<li><a href="/usersearch/">User Search</a></li>
							</ul>
						</li>
						
						<?php echo '<li><p>Hello, ' . strtolower($_SERVER['REMOTE_USER']) . '!</p></li>' ?>
					</ul>
				</div><!--close mainnav-->
			</div><!--close header-->