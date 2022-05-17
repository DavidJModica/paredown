# paredown

Paredown is a plugin file that when installed adds a WP_CLI command that will take two tags and apply the 1st (New Tag) to all posts with the 2nd (Source Tag). There is also an option to delete the 2nd tag afterwards.

-----

## Example

```
$ wp paredown tagparedown 'Elon' 'Elon Musk' 'delete'
```

This would apply `Elon` to all posts that have `Elon Musk` as a tag. It would then delete the `Elon Musk` tag.

To delete the 3rd arguement must be `delete`.

If the New Tag doesn't exist it will be created. If the Source Tag doesn't exist nothing will be done as there will be no posts with that tag.


------

## Possible extentions

* Allow multiple Source Tags(paredownmulti does this)
* Find tags that are substrings of the New Tag and use those are source tags. Example if LGBTQIA is the New Tag we could search for tags like LGBTQ, LGBTQI or LGBT and have them all consolidated into the LGBTQIA tag.
* We could also do a similar thing using something like levenshtein() to find similar tags.
* To use those without issue we likely want more of a UI as we would want to allow for some manual review before we mass delete tags. We first want to display a list of similar tags and then choose which ones to use as Source Tag(s).
