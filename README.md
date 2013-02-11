# Narrator for Twilio

This Laravel 4 (beta) application will take any semantically-formatted [markdown](http://daringfireball.net/projects/markdown/syntax) document and output it in [TwiML](https://www.twilio.com/docs/api/twiml), which allows it to be navigated as an [IVR](http://en.wikipedia.org/wiki/Interactive_voice_response).

## System Requirements

- PHP 5.3.2 or greater
- [PHP DOM extensions](http://www.php.net/manual/en/book.dom.php)
- [Composer](http://getcomposer.org/)
- [Laravel 4](http://four.laravel.com/) (beta)

## Document Compatability

Narrator expects to receive a document in Daring Fireball's [markdown syntax](http://daringfireball.net/projects/markdown/syntax).  This software will not validate the document; and malformed documents may cause a user to be disconnected from the system.

In addition to the required syntax, the document must be semantically-formatted.  Specifically, Narrator expects to encounter one heading at level one and it will be assumed to be the title for the document.
```markdown
# Document Title
```
Any and all additional subheadings must be at level two or greater.
```markdown
## Sub Heading
```