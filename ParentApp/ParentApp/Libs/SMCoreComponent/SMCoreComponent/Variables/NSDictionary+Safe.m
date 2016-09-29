//
//  NSDictionary+Safe.m
//  SMCoreComponent
//
//  Created by Shilpa Gade on 04/03/15.
//  Copyright (c) 2015 sofmen. All rights reserved.
//

#import "NSDictionary+Safe.h"

@implementation NSDictionary (Safe)
- (id)safeObjectForKey:(id)aKey{
    id object = [self objectForKey:aKey];
    if ([object isKindOfClass:[NSNull class]] || object==NULL || object==nil) {
        return @"";
    }
    return object;
}
@end
