# Question 2: WordPress Image Upload Troubleshooting

## Answer

I would investigate the issue systematically, starting with the affected editor's account and comparing it with the colleague's account. I would avoid changing permissions or server settings until I had evidence identifying the cause.

## Likely Causes and Investigation Order

| Order | Possible Cause | Evidence to Check | Action Once Confirmed |
|---|---|---|---|
| 1 | Insufficient user permissions or role capabilities | Compare both users' WordPress roles and capabilities, including `upload_files`. Check whether a plugin or custom role configuration restricts uploads. | Assign the appropriate role or capability through the site's established permissions process. Avoid granting unnecessary administrator privileges. |
| 2 | Browser or plugin conflict | Check the browser console and Network tab for failed requests. Test in a private window or another browser, and review recent plugin updates. | Update or reconfigure the conflicting component. If necessary, reproduce the issue in staging and isolate the plugin conflict safely. |
| 3 | File restrictions or upload limits | Check the file type, file size, PHP upload limits, WordPress media settings, and the actual error message. Compare with a small, supported image. | Correct the relevant configuration or use an approved image format and size. |

## How I Would Verify the Fix

1. **Reproduce the problem:** Attempt to upload an image using the affected editor's account.
2. **Confirm the cause:** Use logs, permission settings, or a controlled test to identify the underlying issue.
3. **Apply the fix:** Make the smallest appropriate change based on the evidence.
4. **Test the upload:** Ask the editor to upload a supported image and confirm that it appears in the WordPress Media Library.
5. **Check for regressions:** Verify that other users' permissions and existing media functionality remain unaffected.

## Conclusion

I would fix the underlying cause rather than give the affected editor administrator access as a workaround. My approach is to reproduce the issue, gather evidence, isolate the cause, apply the appropriate fix, and verify the result.
