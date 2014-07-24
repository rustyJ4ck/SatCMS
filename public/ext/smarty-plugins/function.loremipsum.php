<?php

/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {loremipsum} plugin
 *
 * Type:     function<br>
 * Name:     loremipsum<br>
 * Purpose:  fixture string
 * Use: {loremipsum count=#chars}
 *
 * @link http://www.smarty.net/manual/en/language.function.fetch.php {fetch}
 *       (Smarty online manual)
 * 
 * @param array                    $params   parameters
 * @param Smarty_Internal_Template $template template object
 * @return string|null if the assign parameter is passed, Smarty assigns the result to a template variable
 */
function smarty_function_loremipsum($params)
{
static $LOREM = 
'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent magna massa, molestie sit amet eleifend sit amet, eleifend id justo. Etiam rutrum purus eu erat rutrum blandit. Morbi fringilla enim vel enim dapibus vitae ullamcorper sem ullamcorper. Donec scelerisque lacus eget lectus pellentesque sit amet rutrum mi ornare. Mauris mollis enim ut urna dignissim sit amet tempus ligula facilisis. Vestibulum ultrices tristique sem, id accumsan libero fermentum vulputate. Integer convallis arcu ac orci vulputate non condimentum urna lobortis. Nulla viverra, enim in sodales euismod, massa velit rhoncus nunc, et rhoncus metus ante ut nunc. Ut facilisis magna ut diam aliquam adipiscing. Aliquam tempor sollicitudin urna quis imperdiet.
In hac habitasse platea dictumst. Vestibulum nec enim dolor, vel cursus lacus. Phasellus sodales dui vel sem consequat eget euismod elit mollis. Vestibulum imperdiet vehicula tellus at luctus. Nunc eget nisi ante, id pretium velit. Morbi et massa commodo velit condimentum fringilla. Morbi magna neque, facilisis ac tincidunt sit amet, commodo at sem. Etiam viverra magna nec arcu tempor aliquet. Fusce rhoncus adipiscing lectus pellentesque lacinia. Nulla ullamcorper, ante id convallis ultricies, turpis turpis sollicitudin dolor, non lobortis tellus arcu at arcu. Curabitur pulvinar iaculis nunc vel tincidunt. Morbi id quam eget libero varius tempor. Quisque ut ligula in risus rhoncus volutpat vel vel urna. Proin consequat, magna rutrum tempus mattis, purus tellus pulvinar arcu, ac dapibus risus diam vel erat.
Vestibulum tincidunt tortor sit amet velit ullamcorper sed scelerisque nulla sagittis. Mauris condimentum, quam et tempor blandit, augue risus pharetra sapien, id feugiat metus velit sit amet nunc. In vel justo odio, sagittis pharetra turpis. Quisque auctor mauris a augue dapibus tristique. In ullamcorper cursus elit at viverra. Phasellus id quam lectus, nec tincidunt dui. Fusce tellus felis, posuere sodales gravida vel, feugiat a sapien. Nam quis laoreet mi. Aliquam vel quam velit, at rutrum enim. Nullam ac sapien at nisl feugiat venenatis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Fusce molestie, sem non vulputate ornare, risus nisl pellentesque magna, id vestibulum dui tellus vel ligula. Vestibulum mattis porttitor facilisis. In risus odio, laoreet sit amet convallis et, elementum eget nisi. Fusce viverra, erat et hendrerit ultrices, diam lectus molestie diam, vitae vehicula sapien justo tempor felis.
Cras tempus, leo in vulputate ultricies, eros nisl sodales est, sed convallis leo quam sed erat. Nulla ac nibh tortor. Curabitur augue nisi, sollicitudin nec volutpat in, pretium in elit. Curabitur non metus sed libero pharetra venenatis. Nullam ut turpis lorem, eu tincidunt odio. Sed et erat justo, viverra porta libero. Donec dapibus dolor ut metus euismod auctor sed sed neque. Etiam eget turpis at elit elementum faucibus. Phasellus vitae sapien nisi, id mattis sem. Mauris adipiscing lacus a nunc lacinia at condimentum leo iaculis. Ut ipsum sem, dictum vitae tristique sit amet, lacinia sit amet turpis. Aenean purus quam, luctus non aliquam vitae, vulputate a velit. Etiam interdum congue lectus non ultrices. Etiam sed odio dolor, a pellentesque elit.
Proin velit mauris, viverra egestas scelerisque eget, vehicula nec quam. Vestibulum lacus nisi, pulvinar nec auctor vel, consectetur accumsan eros. Duis ut elit auctor magna gravida gravida. Integer mauris nunc, ultrices eu semper a, varius a ipsum. Nunc mollis vulputate felis in fringilla. Etiam nec justo metus, dapibus fringilla nunc. Quisque congue leo eu ipsum semper accumsan. Vivamus ut mi lorem. Nulla facilisi. Mauris in odio sed felis dapibus eleifend. Nunc dapibus nisl vitae neque aliquam eget ultricies nunc malesuada. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Fusce venenatis velit ut dolor convallis dignissim.
Sed sollicitudin porta dui non viverra. In hac habitasse platea dictumst. Sed egestas, risus eget euismod iaculis, nisi mi rhoncus purus, nec luctus lacus metus et ante. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis eu metus nunc. Donec semper eleifend neque eget sodales. Vivamus quis diam sem, sit amet molestie libero. Vivamus dui ipsum, aliquet id bibendum tempus, luctus eget elit. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Maecenas in sapien sapien.
Cras pellentesque lorem sodales leo hendrerit a convallis est hendrerit. Nunc eget neque nec magna scelerisque faucibus. Donec vehicula consequat tortor, a condimentum felis laoreet sed. Nunc est nibh, volutpat vel sollicitudin nec, rhoncus a nibh. Nulla facilisi. Phasellus consectetur mauris sit amet mauris eleifend iaculis. Duis volutpat lectus tincidunt velit sodales ac porttitor est porttitor. Nunc placerat, leo a facilisis iaculis, nunc sem tristique ante, quis pharetra magna dolor sit amet arcu. Aliquam nec est odio. Nulla pellentesque enim non risus malesuada dictum. Quisque sapien nisi, dictum eu tempor porttitor, dapibus quis tortor. Aliquam pulvinar leo sed tellus dictum fringilla.
Fusce auctor nisi vitae dolor tempus ornare. In non molestie ipsum. Donec a nisi non urna faucibus consectetur sit amet vel quam. Ut sodales pharetra vehicula. Aenean in massa arcu. Pellentesque dignissim semper dui ac vulputate. Sed fermentum venenatis tempor. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nulla eleifend, enim nec vehicula pharetra, metus nibh iaculis eros, sodales ultrices est mi a magna. Nulla facilisi. Morbi mauris nisl, sodales at pulvinar eget, malesuada vitae odio.
Fusce ultricies sollicitudin mollis. Aenean bibendum, erat a porttitor vestibulum, odio quam volutpat urna, ut condimentum justo tortor vitae sapien. Nulla a volutpat odio. Curabitur vel orci nisi. Curabitur sodales eleifend nisl, id dignissim est ultricies sit amet. Cras sit amet lectus metus. Etiam vitae fringilla quam. Nam adipiscing vehicula mi, in posuere orci dignissim sed. Nam pretium lobortis lectus nec dapibus. Phasellus vel tempor nisi.';
    
    $count = !isset($params['count']) ? 255 : $params['count'];
    
    return str_replace("\n","\n<br/><br/>", substr($LOREM, 0, $count));
}