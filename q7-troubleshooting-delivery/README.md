# Question 7: Troubleshooting and Delivery

## Assumptions

I would assume the article was corrected in WordPress and the theme release may have changed its rendering. I would not assume the cause is caching: the publishing stack may include browser caching, a CDN, a reverse proxy, WordPress page/object caches, and more than one application instance.

## First Three Checks

1. Reproduce from two clean, logged-out sessions and compare a normal request with a cache-bypassing request. I would record the URL, response status, `Cache-Control`, `Age`, `ETag`, CDN cache headers, and the displayed article revision. I would repeat this from more than one network or CDN region if available.
2. Check the source of truth. I would confirm the published WordPress revision and permalink in the admin and REST API, then request the page directly from the origin. I would also verify the deployed theme version or build identifier and check whether every application instance runs the same release.
3. Trace cache invalidation and deployment health. I would check CDN purge history and tags, reverse-proxy/page-cache keys, service-worker behaviour, and application/error logs. I would confirm that the article URL and any related fragments were purged and that the release completed on every node.

## Distinguishing the Cause

If the origin and a cache-bypassing request show the corrected article while a normal request shows the old article, the evidence points to a cache layer or stale client asset. If WordPress itself, the REST API, or the origin still shows the old content, it is a content or publishing-state problem. If the origin varies by application instance or the template differs despite the same content revision, it is a deployment, code, or configuration consistency problem.

I would avoid an immediate blind release. A second deployment can make cache diagnosis harder and can spread a fault to more instances.

## Rollback and Recovery

I would roll back when the release causes a material reader-facing regression, such as the wrong article content, broken rendering, a security issue, or a sustained high error rate, and the previous known-good artifact is available. I would not roll back a confirmed cache-only incident until first attempting the narrow, reversible fix: purge the relevant cache keys and verify propagation.

Recovery is verified by checking the expected revision and rendered page through normal and cache-bypass requests, from at least two locations, plus a logged-in and logged-out session if those responses can differ. I would confirm cache headers now identify the fresh response, error and latency metrics return to baseline, and no stale application instance remains.

## Editor Update

“I am checking the published revision, origin response, and cache layers before releasing again, because the mixed results may be caused by stale caching rather than the theme code. I will either purge the affected cache or roll back to the last known-good release based on that evidence, then confirm the corrected article from independent logged-in and logged-out checks.”
