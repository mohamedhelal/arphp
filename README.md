بسم الله الرحمن الرحيم
==================
فريم ورك لتسهيل البرمجة على المبرمجين  وتسهيل التطوير
ArPHP
 * * *
ملفات routes التى تتحكم فى العرض فى الموقع

روابط لوحة التحكم
```php
app/routes/admin.php
```
روابط ال api
```php
app/routes/api.php
```
روابط الرئيسية
```php
app/routes/front.php
```

فكرة الروابط هى ان ممكن تتحكم فى طريقة طلب الرابط يعنى ممكن يكون
methods 
'POST', 'GET', 'HEAD', 'PUT', 'INSERT', 'UPDATE', 'DELETE', 'SELECT'

النيم اسبيس الافتراضى
App\Http\Controllers
```php
Route::get('/','HomeController@index');
// App\Http\Controllers\HomeController@index
```
```php
Route::post('/','HomeController@index');
// App\Http\Controllers\HomeController@index
```
```php
Route::head('/','HomeController@index');
// App\Http\Controllers\HomeController@index
```
```php
Route::put('/','HomeController@index');
// App\Http\Controllers\HomeController@index
```
```php
Route::insert('/','HomeController@index');
// App\Http\Controllers\HomeController@index
```
```php
Route::update('/','HomeController@index');
// App\Http\Controllers\HomeController@index
```
```php
Route::delete('/','HomeController@index');
// App\Http\Controllers\HomeController@index
```
```php
Route::select('/','HomeController@index');
// App\Http\Controllers\HomeController@index
```


هيتم عرض فقط الصفحة لما تكون مطلوبه من المتصفح فى الحالات ديه
