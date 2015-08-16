# README #

**.Desktop** is an Ubuntu .desktop file editor allowing you to create/edit Ubuntu's .desktop file launchers from the comfort of your own browser.

The original concept began life as a Python GUI Application but I decided, "Why bother with Python? Just do it all in a locally hosted Web Application!"

My original project utilised Composer and Bower to ensure that the external libraries used by **.Desktop** were easily updated.

I decided, however, to make life easier for those using this repo by removing Composer and Bower from the project and rely soley on CDN support and internalization of the libraries that have no CDN support.

The two libraries with no CDN support are; [sweetalert2](https://limonte.github.io/sweetalert2/) and [yii2-scroll-top](https://github.com/bluezed/yii2-scroll-top). The internalization of these two libraries require manual updates when new sources are available.

For the most part, I followed [Ubuntu's recommendations](https://design.ubuntu.com/brand/colour-palette) for the application's color scheme. I did, admittedly, diverge from Ubuntu's recommendations, here and there, to exercise my "Artistic License".

### Caveats ###

* The application uses the **superglobal $_POST[]** variable in a __completely UNFILTERED__ way.

    **DO NOT ATTEMPT TO USE THIS CODE ON A PUBLIC FACING SERVER!!!! EVER!!!**
    
* If your desktop files aren't located in **/usr/share/applications/**, modify line 18 of the index.php file as appropriate.
* The application has **not** been cross-browser or 'mobile' tested _(you may need to make some minor layout adjustments)_.

### Using .Desktop ###

Its all pretty straight-forward...

If you want to edit a _section name_ (the title of the box), an _item name_ (the title to the left of the edit boxes) or a hash-tagged(#) _comment block_ (appears as a blockquote), simply click on the title or comment block you want to edit.

If you make any changes to titles, item values, etc. a **Save** button will appear at the top of the page which will allow you to write the current contents of the .desktop file to the application's "output" folder (using the same file name as the one you began with). 

Saves will overwrite any previously existing file of the same name that is in the "output" folder.

Once you save an edited .desktop file, manually move/copy the file from the application's "output" folder to the appropriate directory in your Ubuntu installation, typically `/usr/share/applications`.
 
![Desktop File Utility.png](https://bitbucket.org/repo/k6RMo6/images/585795466-Desktop%20File%20Utility.png)

## Enjoy! ##