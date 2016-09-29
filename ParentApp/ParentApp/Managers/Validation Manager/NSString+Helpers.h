//
//  NSString+Helpers.h
//  WordPress
//
//  Created by John Bickerstaff on 9/9/09.
// //

#import <UIKit/UIKit.h>

@interface NSString (Helpers)

// helper functions
- (NSString *) stringByUrlEncoding;
- (NSString *) base64Encoding;
- (NSString *) trim;
- (BOOL) startsWith:(NSString *)s;
- (BOOL) containsString:(NSString * )aString;
- (NSString *)substringFrom:(NSInteger)a to:(NSInteger)b;
@end
