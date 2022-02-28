/*! For license information please see settings-vue-settings-apps-users-management.js.LICENSE.txt */
!function(){"use strict";var e,r,o,i={31579:function(e,r,o){var i=o(20144),u=o(34741),a=o(83678),s={name:"App",beforeMount:function(){null!==document.getElementById("serverData")&&this.$store.commit("setServerData",JSON.parse(document.getElementById("serverData").dataset.server))}},c=(0,o(51900).Z)(s,(function(){var t=this.$createElement;return(this._self._c||t)("router-view")}),[],!1,null,null,null).exports,d=o(78345),p=o(79753),f=function(){return Promise.all([o.e(874),o.e(351)]).then(o.bind(o,98902))},l=function(){return Promise.all([o.e(874),o.e(418)]).then(o.bind(o,78422))};i.default.use(d.Z);var m=new d.Z({mode:"history",base:(0,p.generateUrl)(""),linkActiveClass:"active",routes:[{path:"/:index(index.php/)?settings/users",component:f,props:!0,name:"users",children:[{path:":selectedGroup",name:"group",component:f}]},{path:"/:index(index.php/)?settings/apps",component:l,props:!0,name:"apps",children:[{path:":category",name:"apps-category",component:l,children:[{path:":id",name:"apps-details",component:l}]}]}]}),g=o(20629),h=o(4820),A=o(10128),v=o.n(A),U=function(t){return t.replace(/\/$/,"")},I=function(){return v()()},b=function(t,e){return h.default.get(U(t),e)},y=function(t,e){return h.default.post(U(t),e)},L=function(t,e){return h.default.put(U(t),e)},P=function(t,e){return h.default.delete(U(t),{params:e})},O=function(t,e){return 1===e?t.sort((function(t,e){return t.usercount-t.disabled<e.usercount-e.disabled})):t.sort((function(t,e){return t.name.localeCompare(e.name)}))},w={id:"",name:"",usercount:0,disabled:0,canAdd:!0,canRemove:!0},E={appendUsers:function(t,e){var r=t.users.concat(Object.keys(e).map((function(t){return e[t]})));t.usersOffset+=t.usersLimit,t.users=r},setPasswordPolicyMinLength:function(t,e){t.minPasswordLength=""!==e?e:0},initGroups:function(t,e){var r=e.groups,n=e.orderBy,o=e.userCount;t.groups=r.map((function(t){return Object.assign({},w,t)})),t.orderBy=n,t.userCount=o,t.groups=O(t.groups,t.orderBy)},addGroup:function(t,e){var r=e.gid,n=e.displayName;try{if(void 0!==t.groups.find((function(t){return t.id===r})))return;var o=Object.assign({},w,{id:r,name:n});t.groups.push(o),t.groups=O(t.groups,t.orderBy)}catch(t){console.error("Can't create group",t)}},renameGroup:function(t,e){var r=e.gid,n=e.displayName,o=t.groups.findIndex((function(t){return t.id===r}));if(o>=0){var i=t.groups[o];i.name=n,t.groups.splice(o,1,i),t.groups=O(t.groups,t.orderBy)}},removeGroup:function(t,e){var r=t.groups.findIndex((function(t){return t.id===e}));r>=0&&t.groups.splice(r,1)},addUserGroup:function(t,e){var r=e.userid,n=e.gid,o=t.groups.find((function(t){return t.id===n})),i=t.users.find((function(t){return t.id===r}));o&&i.enabled&&t.userCount>0&&o.usercount++,i.groups.push(n),t.groups=O(t.groups,t.orderBy)},removeUserGroup:function(t,e){var r=e.userid,n=e.gid,o=t.groups.find((function(t){return t.id===n})),i=t.users.find((function(t){return t.id===r}));o&&i.enabled&&t.userCount>0&&o.usercount--;var u=i.groups;u.splice(u.indexOf(n),1),t.groups=O(t.groups,t.orderBy)},addUserSubAdmin:function(t,e){var r=e.userid,n=e.gid;t.users.find((function(t){return t.id===r})).subadmin.push(n)},removeUserSubAdmin:function(t,e){var r=e.userid,n=e.gid,o=t.users.find((function(t){return t.id===r})).subadmin;o.splice(o.indexOf(n),1)},deleteUser:function(t,e){var r=t.users.findIndex((function(t){return t.id===e}));t.users.splice(r,1)},addUserData:function(t,e){t.users.push(e.data.ocs.data)},enableDisableUser:function(t,e){var r=e.userid,n=e.enabled,o=t.users.find((function(t){return t.id===r}));o.enabled=n,t.userCount>0&&(t.groups.find((function(t){return"disabled"===t.id})).usercount+=n?-1:1,t.userCount+=n?1:-1,o.groups.forEach((function(e){t.groups.find((function(t){return t.id===e})).disabled+=n?-1:1})))},setUserData:function(t,e){var r=e.userid,n=e.key,o=e.value;if("quota"===n){var i=OC.Util.computerFileSize(o);t.users.find((function(t){return t.id===r}))[n][n]=null!==i?i:o}else t.users.find((function(t){return t.id===r}))[n]=o},resetUsers:function(t){t.users=[],t.usersOffset=0}},C=h.default.CancelToken,_=null,R={state:{users:[],groups:[],orderBy:1,minPasswordLength:0,usersOffset:0,usersLimit:25,userCount:0},mutations:E,getters:{getUsers:function(t){return t.users},getGroups:function(t){return t.groups},getSubadminGroups:function(t){return t.groups.filter((function(t){return"admin"!==t.id&&"disabled"!==t.id}))},getPasswordPolicyMinLength:function(t){return t.minPasswordLength},getUsersOffset:function(t){return t.usersOffset},getUsersLimit:function(t){return t.usersLimit},getUserCount:function(t){return t.userCount}},actions:{getUsers:function(t,e){var r=e.offset,n=e.limit,o=e.search,i=e.group;return _&&_.cancel("Operation canceled by another search request."),_=C.source(),o="string"==typeof o?o:"",""!==(i="string"==typeof i?i:"")?b((0,p.generateOcsUrl)("cloud/groups/{group}/users/details?offset={offset}&limit={limit}&search={search}",{group:encodeURIComponent(i),offset:r,limit:n,search:o}),{cancelToken:_.token}).then((function(e){var r=Object.keys(e.data.ocs.data.users).length;return r>0&&t.commit("appendUsers",e.data.ocs.data.users),r})).catch((function(e){h.default.isCancel(e)||t.commit("API_FAILURE",e)})):b((0,p.generateOcsUrl)("cloud/users/details?offset={offset}&limit={limit}&search={search}",{offset:r,limit:n,search:o}),{cancelToken:_.token}).then((function(e){var r=Object.keys(e.data.ocs.data.users).length;return r>0&&t.commit("appendUsers",e.data.ocs.data.users),r})).catch((function(e){h.default.isCancel(e)||t.commit("API_FAILURE",e)}))},getGroups:function(t,e){var r=e.offset,n=e.limit,o=e.search;o="string"==typeof o?o:"";var i=-1===n?"":"&limit=".concat(n);return b((0,p.generateOcsUrl)("cloud/groups?offset={offset}&search={search}",{offset:r,search:o})+i).then((function(e){return Object.keys(e.data.ocs.data.groups).length>0&&(e.data.ocs.data.groups.forEach((function(e){t.commit("addGroup",{gid:e,displayName:e})})),!0)})).catch((function(e){return t.commit("API_FAILURE",e)}))},getUsersFromList:function(t,e){var r=e.offset,n=e.limit,o=e.search;return o="string"==typeof o?o:"",b((0,p.generateOcsUrl)("cloud/users/details?offset={offset}&limit={limit}&search={search}",{offset:r,limit:n,search:o})).then((function(e){return Object.keys(e.data.ocs.data.users).length>0&&(t.commit("appendUsers",e.data.ocs.data.users),!0)})).catch((function(e){return t.commit("API_FAILURE",e)}))},getUsersFromGroup:function(t,e){var r=e.groupid,n=e.offset,o=e.limit;return b((0,p.generateOcsUrl)("cloud/users/{groupId}/details?offset={offset}&limit={limit}",{groupId:encodeURIComponent(r),offset:n,limit:o})).then((function(e){return t.commit("getUsersFromList",e.data.ocs.data.users)})).catch((function(e){return t.commit("API_FAILURE",e)}))},getPasswordPolicyMinLength:function(t){return!(!OC.getCapabilities().password_policy||!OC.getCapabilities().password_policy.minLength)&&(t.commit("setPasswordPolicyMinLength",OC.getCapabilities().password_policy.minLength),OC.getCapabilities().password_policy.minLength)},addGroup:function(t,e){return I().then((function(r){return y((0,p.generateOcsUrl)("cloud/groups"),{groupid:e}).then((function(r){return t.commit("addGroup",{gid:e,displayName:e}),{gid:e,displayName:e}})).catch((function(t){throw t}))})).catch((function(r){throw t.commit("API_FAILURE",{gid:e,error:r}),r}))},renameGroup:function(t,e){var r=e.groupid,n=e.displayName;return I().then((function(e){return L((0,p.generateOcsUrl)("cloud/groups/{groupId}",{groupId:encodeURIComponent(r)}),{key:"displayname",value:n}).then((function(e){return t.commit("renameGroup",{gid:r,displayName:n}),{groupid:r,displayName:n}})).catch((function(t){throw t}))})).catch((function(e){throw t.commit("API_FAILURE",{groupid:r,error:e}),e}))},removeGroup:function(t,e){return I().then((function(r){return P((0,p.generateOcsUrl)("cloud/groups/{groupId}",{groupId:encodeURIComponent(e)})).then((function(r){return t.commit("removeGroup",e)})).catch((function(t){throw t}))})).catch((function(r){return t.commit("API_FAILURE",{gid:e,error:r})}))},addUserGroup:function(t,e){var r=e.userid,n=e.gid;return I().then((function(e){return y((0,p.generateOcsUrl)("cloud/users/{userid}/groups",{userid:r}),{groupid:n}).then((function(e){return t.commit("addUserGroup",{userid:r,gid:n})})).catch((function(t){throw t}))})).catch((function(e){return t.commit("API_FAILURE",{userid:r,error:e})}))},removeUserGroup:function(t,e){var r=e.userid,n=e.gid;return I().then((function(e){return P((0,p.generateOcsUrl)("cloud/users/{userid}/groups",{userid:r}),{groupid:n}).then((function(e){return t.commit("removeUserGroup",{userid:r,gid:n})})).catch((function(t){throw t}))})).catch((function(e){throw t.commit("API_FAILURE",{userid:r,error:e}),e}))},addUserSubAdmin:function(t,e){var r=e.userid,n=e.gid;return I().then((function(e){return y((0,p.generateOcsUrl)("cloud/users/{userid}/subadmins",{userid:r}),{groupid:n}).then((function(e){return t.commit("addUserSubAdmin",{userid:r,gid:n})})).catch((function(t){throw t}))})).catch((function(e){return t.commit("API_FAILURE",{userid:r,error:e})}))},removeUserSubAdmin:function(t,e){var r=e.userid,n=e.gid;return I().then((function(e){return P((0,p.generateOcsUrl)("cloud/users/{userid}/subadmins",{userid:r}),{groupid:n}).then((function(e){return t.commit("removeUserSubAdmin",{userid:r,gid:n})})).catch((function(t){throw t}))})).catch((function(e){return t.commit("API_FAILURE",{userid:r,error:e})}))},wipeUserDevices:function(t,e){return I().then((function(t){return y((0,p.generateOcsUrl)("cloud/users/{userid}/wipe",{userid:e})).catch((function(t){throw t}))})).catch((function(r){return t.commit("API_FAILURE",{userid:e,error:r})}))},deleteUser:function(t,e){return I().then((function(r){return P((0,p.generateOcsUrl)("cloud/users/{userid}",{userid:e})).then((function(r){return t.commit("deleteUser",e)})).catch((function(t){throw t}))})).catch((function(r){return t.commit("API_FAILURE",{userid:e,error:r})}))},addUser:function(t,e){var r=t.commit,n=t.dispatch,o=e.userid,i=e.password,u=e.displayName,a=e.email,s=e.groups,c=e.subadmin,d=e.quota,f=e.language;return I().then((function(t){return y((0,p.generateOcsUrl)("cloud/users"),{userid:o,password:i,displayName:u,email:a,groups:s,subadmin:c,quota:d,language:f}).then((function(t){return n("addUserData",o||t.data.ocs.data.id)})).catch((function(t){throw t}))})).catch((function(t){throw r("API_FAILURE",{userid:o,error:t}),t}))},addUserData:function(t,e){return I().then((function(r){return b((0,p.generateOcsUrl)("cloud/users/{userid}",{userid:e})).then((function(e){return t.commit("addUserData",e)})).catch((function(t){throw t}))})).catch((function(r){return t.commit("API_FAILURE",{userid:e,error:r})}))},enableDisableUser:function(t,e){var r=e.userid,n=e.enabled,o=void 0===n||n,i=o?"enable":"disable";return I().then((function(e){return L((0,p.generateOcsUrl)("cloud/users/{userid}/{userStatus}",{userid:r,userStatus:i})).then((function(e){return t.commit("enableDisableUser",{userid:r,enabled:o})})).catch((function(t){throw t}))})).catch((function(e){return t.commit("API_FAILURE",{userid:r,error:e})}))},setUserData:function(t,e){var r=e.userid,n=e.key,o=e.value,i=["email","displayname"];return-1!==["email","language","quota","displayname","password"].indexOf(n)&&"string"==typeof o&&(-1===i.indexOf(n)&&o.length>0||-1!==i.indexOf(n))?I().then((function(e){return L((0,p.generateOcsUrl)("cloud/users/{userid}",{userid:r}),{key:n,value:o}).then((function(e){return t.commit("setUserData",{userid:r,key:n,value:o})})).catch((function(t){throw t}))})).catch((function(e){return t.commit("API_FAILURE",{userid:r,error:e})})):Promise.reject(new Error("Invalid request data"))},sendWelcomeMail:function(t,e){return I().then((function(t){return y((0,p.generateOcsUrl)("cloud/users/{userid}/welcome",{userid:e})).then((function(t){return!0})).catch((function(t){throw t}))})).catch((function(r){return t.commit("API_FAILURE",{userid:e,error:r})}))}}},F=o(26932),k=(o(73317),{APPS_API_FAILURE:function(e,r){(0,F.x2)(t("settings","An error occured during the request. Unable to proceed.")+"<br>"+r.error.response.data.data.message,{timeout:7,isHTML:!0}),console.error(e,r)},initCategories:function(t,e){var r=e.categories,n=e.updateCount;t.categories=r,t.updateCount=n},setUpdateCount:function(t,e){t.updateCount=e},addCategory:function(t,e){t.categories.push(e)},appendCategories:function(t,e){t.categories=e},setAllApps:function(t,e){t.apps=e},setError:function(t,e){var r=e.appId,n=e.error;Array.isArray(r)||(r=[r]),r.forEach((function(e){t.apps.find((function(t){return t.id===e})).error=n}))},clearError:function(t,e){var r=e.appId;e.error,t.apps.find((function(t){return t.id===r})).error=null},enableApp:function(t,e){var r=e.appId,n=e.groups,o=t.apps.find((function(t){return t.id===r}));o.active=!0,o.groups=n},disableApp:function(t,e){var r=t.apps.find((function(t){return t.id===e}));r.active=!1,r.groups=[],r.removable&&(r.canUnInstall=!0)},uninstallApp:function(t,e){t.apps.find((function(t){return t.id===e})).active=!1,t.apps.find((function(t){return t.id===e})).groups=[],t.apps.find((function(t){return t.id===e})).needsDownload=!0,t.apps.find((function(t){return t.id===e})).installed=!1,t.apps.find((function(t){return t.id===e})).canUnInstall=!1,t.apps.find((function(t){return t.id===e})).canInstall=!0},updateApp:function(t,e){var r=t.apps.find((function(t){return t.id===e})),n=r.update;r.update=null,r.version=n,t.updateCount--},resetApps:function(t){t.apps=[]},reset:function(t){t.apps=[],t.categories=[],t.updateCount=0},startLoading:function(t,e){Array.isArray(e)?e.forEach((function(e){i.default.set(t.loading,e,!0)})):i.default.set(t.loading,e,!0)},stopLoading:function(t,e){Array.isArray(e)?e.forEach((function(e){i.default.set(t.loading,e,!1)})):i.default.set(t.loading,e,!1)}}),S={enableApp:function(e,r){var n,o=r.appId,i=r.groups;return n=Array.isArray(o)?o:[o],I().then((function(r){return e.commit("startLoading",n),e.commit("startLoading","install"),y((0,p.generateUrl)("settings/apps/enable"),{appIds:n,groups:i}).then((function(r){return e.commit("stopLoading",n),e.commit("stopLoading","install"),n.forEach((function(t){e.commit("enableApp",{appId:t,groups:i})})),b((0,p.generateUrl)("apps/files")).then((function(){r.data.update_required&&((0,F.JQ)(t("settings","The app has been enabled but needs to be updated. You will be redirected to the update page in 5 seconds."),{onClick:function(){return window.location.reload()},close:!1}),setTimeout((function(){location.reload()}),5e3))})).catch((function(){Array.isArray(o)||e.commit("setError",{appId:n,error:t("settings","Error: This app cannot be enabled because it makes the server unstable")})}))})).catch((function(t){e.commit("stopLoading",n),e.commit("stopLoading","install"),e.commit("setError",{appId:n,error:t.response.data.data.message}),e.commit("APPS_API_FAILURE",{appId:o,error:t})}))})).catch((function(t){return e.commit("API_FAILURE",{appId:o,error:t})}))},forceEnableApp:function(t,e){var r,n=e.appId;return e.groups,r=Array.isArray(n)?n:[n],I().then((function(){return t.commit("startLoading",r),t.commit("startLoading","install"),y((0,p.generateUrl)("settings/apps/force"),{appId:n}).then((function(t){location.reload()})).catch((function(e){t.commit("stopLoading",r),t.commit("stopLoading","install"),t.commit("setError",{appId:r,error:e.response.data.data.message}),t.commit("APPS_API_FAILURE",{appId:n,error:e})}))})).catch((function(e){return t.commit("API_FAILURE",{appId:n,error:e})}))},disableApp:function(t,e){var r,n=e.appId;return r=Array.isArray(n)?n:[n],I().then((function(e){return t.commit("startLoading",r),y((0,p.generateUrl)("settings/apps/disable"),{appIds:r}).then((function(e){return t.commit("stopLoading",r),r.forEach((function(e){t.commit("disableApp",e)})),!0})).catch((function(e){t.commit("stopLoading",r),t.commit("APPS_API_FAILURE",{appId:n,error:e})}))})).catch((function(e){return t.commit("API_FAILURE",{appId:n,error:e})}))},uninstallApp:function(t,e){var r=e.appId;return I().then((function(e){return t.commit("startLoading",r),b((0,p.generateUrl)("settings/apps/uninstall/".concat(r))).then((function(e){return t.commit("stopLoading",r),t.commit("uninstallApp",r),!0})).catch((function(e){t.commit("stopLoading",r),t.commit("APPS_API_FAILURE",{appId:r,error:e})}))})).catch((function(e){return t.commit("API_FAILURE",{appId:r,error:e})}))},updateApp:function(t,e){var r=e.appId;return I().then((function(e){return t.commit("startLoading",r),t.commit("startLoading","install"),b((0,p.generateUrl)("settings/apps/update/".concat(r))).then((function(e){return t.commit("stopLoading","install"),t.commit("stopLoading",r),t.commit("updateApp",r),!0})).catch((function(e){t.commit("stopLoading",r),t.commit("stopLoading","install"),t.commit("APPS_API_FAILURE",{appId:r,error:e})}))})).catch((function(e){return t.commit("API_FAILURE",{appId:r,error:e})}))},getAllApps:function(t){return t.commit("startLoading","list"),b((0,p.generateUrl)("settings/apps/list")).then((function(e){return t.commit("setAllApps",e.data.apps),t.commit("stopLoading","list"),!0})).catch((function(e){return t.commit("API_FAILURE",e)}))},getCategories:function(t){return t.commit("startLoading","categories"),b((0,p.generateUrl)("settings/apps/categories")).then((function(e){return e.data.length>0&&(t.commit("appendCategories",e.data),t.commit("stopLoading","categories"),!0)})).catch((function(e){return t.commit("API_FAILURE",e)}))}},x={state:{apps:[],categories:[],updateCount:0,loading:{},loadingList:!1},mutations:k,getters:{loading:function(t){return function(e){return t.loading[e]}},getCategories:function(t){return t.categories},getAllApps:function(t){return t.apps},getUpdateCount:function(t){return t.updateCount}},actions:S},G={state:{},mutations:{},getters:{},actions:{setAppConfig:function(t,e){var r=e.app,n=e.key,o=e.value;return I().then((function(t){return y((0,p.generateOcsUrl)("apps/provisioning_api/api/v1/config/apps/{app}/{key}",{app:r,key:n}),{value:o}).catch((function(t){throw t}))})).catch((function(e){return t.commit("API_FAILURE",{app:r,key:n,value:o,error:e})}))}}};i.default.use(g.ZP);var D={API_FAILURE:function(e,r){try{var n=r.error.response.data.ocs.meta.message;OC.Notification.showHtml(t("settings","An error occured during the request. Unable to proceed.")+"<br>"+n,{timeout:7})}catch(e){OC.Notification.showTemporary(t("settings","An error occured during the request. Unable to proceed."))}console.error(e,r)}},j=new g.yh({modules:{users:R,apps:x,settings:{state:{serverData:{}},mutations:{setServerData:function(t,e){t.serverData=e}},getters:{getServerData:function(t){return t.serverData}},actions:{}},oc:G},strict:!1,mutations:D});i.default.use(u.default,{defaultHtml:!1}),(0,a.Z)(j,m),o.nc=btoa(OC.requestToken),i.default.prototype.t=t,i.default.prototype.n=n,i.default.prototype.OC=OC,i.default.prototype.OCA=OCA,i.default.prototype.oc_userconfig=oc_userconfig,new i.default({router:m,store:j,render:function(t){return t(c)}}).$mount("#content")}},u={};function a(t){var e=u[t];if(void 0!==e)return e.exports;var r=u[t]={id:t,loaded:!1,exports:{}};return i[t].call(r.exports,r,r.exports,a),r.loaded=!0,r.exports}a.m=i,a.amdD=function(){throw new Error("define cannot be used indirect")},a.amdO={},e=[],a.O=function(t,r,n,o){if(!r){var i=1/0;for(d=0;d<e.length;d++){r=e[d][0],n=e[d][1],o=e[d][2];for(var u=!0,s=0;s<r.length;s++)(!1&o||i>=o)&&Object.keys(a.O).every((function(t){return a.O[t](r[s])}))?r.splice(s--,1):(u=!1,o<i&&(i=o));if(u){e.splice(d--,1);var c=n();void 0!==c&&(t=c)}}return t}o=o||0;for(var d=e.length;d>0&&e[d-1][2]>o;d--)e[d]=e[d-1];e[d]=[r,n,o]},a.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return a.d(e,{a:e}),e},a.d=function(t,e){for(var r in e)a.o(e,r)&&!a.o(t,r)&&Object.defineProperty(t,r,{enumerable:!0,get:e[r]})},a.f={},a.e=function(t){return Promise.all(Object.keys(a.f).reduce((function(e,r){return a.f[r](t,e),e}),[]))},a.u=function(t){return{351:"settings-users",418:"settings-apps-view"}[t]+"-"+t+".js?v="+{351:"a6c3bd4e9f1198aae27c",418:"69e86dc5a2f12e9ee6e5"}[t]},a.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(t){if("object"==typeof window)return window}}(),a.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},r={},o="nextcloud:",a.l=function(t,e,n,i){if(r[t])r[t].push(e);else{var u,s;if(void 0!==n)for(var c=document.getElementsByTagName("script"),d=0;d<c.length;d++){var p=c[d];if(p.getAttribute("src")==t||p.getAttribute("data-webpack")==o+n){u=p;break}}u||(s=!0,(u=document.createElement("script")).charset="utf-8",u.timeout=120,a.nc&&u.setAttribute("nonce",a.nc),u.setAttribute("data-webpack",o+n),u.src=t),r[t]=[e];var f=function(e,n){u.onerror=u.onload=null,clearTimeout(l);var o=r[t];if(delete r[t],u.parentNode&&u.parentNode.removeChild(u),o&&o.forEach((function(t){return t(n)})),e)return e(n)},l=setTimeout(f.bind(null,void 0,{type:"timeout",target:u}),12e4);u.onerror=f.bind(null,u.onerror),u.onload=f.bind(null,u.onload),s&&document.head.appendChild(u)}},a.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},a.nmd=function(t){return t.paths=[],t.children||(t.children=[]),t},a.j=562,function(){var t;a.g.importScripts&&(t=a.g.location+"");var e=a.g.document;if(!t&&e&&(e.currentScript&&(t=e.currentScript.src),!t)){var r=e.getElementsByTagName("script");r.length&&(t=r[r.length-1].src)}if(!t)throw new Error("Automatic publicPath is not supported in this browser");t=t.replace(/#.*$/,"").replace(/\?.*$/,"").replace(/\/[^\/]+$/,"/"),a.p=t}(),function(){a.b=document.baseURI||self.location.href;var t={562:0};a.f.j=function(e,r){var n=a.o(t,e)?t[e]:void 0;if(0!==n)if(n)r.push(n[2]);else{var o=new Promise((function(r,o){n=t[e]=[r,o]}));r.push(n[2]=o);var i=a.p+a.u(e),u=new Error;a.l(i,(function(r){if(a.o(t,e)&&(0!==(n=t[e])&&(t[e]=void 0),n)){var o=r&&("load"===r.type?"missing":r.type),i=r&&r.target&&r.target.src;u.message="Loading chunk "+e+" failed.\n("+o+": "+i+")",u.name="ChunkLoadError",u.type=o,u.request=i,n[1](u)}}),"chunk-"+e,e)}},a.O.j=function(e){return 0===t[e]};var e=function(e,r){var n,o,i=r[0],u=r[1],s=r[2],c=0;if(i.some((function(e){return 0!==t[e]}))){for(n in u)a.o(u,n)&&(a.m[n]=u[n]);if(s)var d=s(a)}for(e&&e(r);c<i.length;c++)o=i[c],a.o(t,o)&&t[o]&&t[o][0](),t[o]=0;return a.O(d)},r=self.webpackChunknextcloud=self.webpackChunknextcloud||[];r.forEach(e.bind(null,0)),r.push=e.bind(null,r.push.bind(r))}();var s=a.O(void 0,[874],(function(){return a(31579)}));s=a.O(s)}();
//# sourceMappingURL=settings-vue-settings-apps-users-management.js.map?v=2d7f3ed2e8a5e642a494