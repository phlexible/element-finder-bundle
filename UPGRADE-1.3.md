1.3.0
=====

Required schema changes:

```sql
ALTER TABLE elementfinder_lookup_element ADD path VARCHAR(255) NOT NULL;
```

Required code changes:

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
