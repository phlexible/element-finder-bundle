1.3.0
=====

 * The class `Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultItem` was deprecated. You
   should use `Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultItem` instead.

   Before:

   ```php
   $resultItem = new \Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultItem();
   ```

   After:

   ```php
   $resultItem = new \Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultItem();
   ```

 * The class `Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultPool` was deprecated. You
   should use `Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultPool` instead.

   Before:

   ```php
   $resultItem = new \Phlexible\Bundle\ElementFinderBundle\ElementFinder\ResultPool();
   ```

   After:

   ```php
   $resultItem = new \Phlexible\Bundle\ElementFinderBundle\ElementFinder\Result\ResultPool();
   ```
