//
//  NSString+Helpers.m
//  WordPress
//
//  Created by John Bickerstaff on 9/9/09.
//  
//

#import "NSString+Helpers.h"
#import "NSData+Base64.h"

@implementation NSString (Helpers)

#pragma mark Helpers
- (NSString *) stringByUrlEncoding
{
	return [(NSString *)CFURLCreateStringByAddingPercentEscapes(NULL,  (CFStringRef)self,  NULL,  (CFStringRef)@"!*'();:@&=+$,/?%#[]",  kCFStringEncodingUTF8) autorelease];
}

- (NSString *)base64Encoding
{
	NSData *stringData = [self dataUsingEncoding:NSUTF8StringEncoding];
	NSString *encodedString = [stringData base64EncodedString];

	return encodedString;
}

- (NSString*)trim 
{
	return [self stringByTrimmingCharactersInSet:[NSCharacterSet
												  whitespaceAndNewlineCharacterSet]];
	
}

- (BOOL)startsWith:(NSString*)s {
	if([self length] < [s length]) return NO;
	return [s isEqualToString:[self substringFrom:0 to:[s length]]];
	
}

- (NSString*)substringFrom:(NSInteger)a to:(NSInteger)b
{
	NSRange r;
	r.location = a;
	r.length = b - a;
	return [self substringWithRange:r];
	
} 
- (BOOL)containsString:(NSString *)aString {
	NSRange range = [[self lowercaseString] rangeOfString:[aString
														   lowercaseString]];
	return range.location != NSNotFound;
}

@end