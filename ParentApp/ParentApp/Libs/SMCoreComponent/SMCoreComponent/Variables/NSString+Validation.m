//
//  NSString+Validation.m
//  SMCoreComponent
//
//  Created by Tushar on 21/02/15.
//  Copyright (c) 2015 sofmen. All rights reserved.
//

#import "NSString+Validation.h"

@implementation NSString (Validation)

-(BOOL) isValidEmail
{
    BOOL stricterFilter = NO;
    NSString *stricterFilterString = @"[A-Z0-9a-z\\._%+-]+@([A-Za-z0-9-]+\\.)+[A-Za-z]{2,4}";
    NSString *laxString = @".+@([A-Za-z0-9-]+\\.)+[A-Za-z]{2}[A-Za-z]*";
    NSString *emailRegex = stricterFilter ? stricterFilterString : laxString;
    NSPredicate *emailTest = [NSPredicate predicateWithFormat:@"SELF MATCHES %@", emailRegex];
    return [emailTest evaluateWithObject:self];
}

-(BOOL) containString:(NSString *)aString;
{
    if ([self rangeOfString:aString].location == NSNotFound) {
        return NO;
    } else {
        return YES;
    }
}
@end
