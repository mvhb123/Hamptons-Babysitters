//
//  NSMutableDictionary+Safe.m
//  SMCoreComponent
//
//  Created by sofmen on 21/02/15.
//  Copyright (c) 2015 sofmen. All rights reserved.
//

#import "NSMutableDictionary+Safe.h"

@implementation NSMutableDictionary (Safe)

- (void)setSafeObject:(id)nonNullObject forKey:(id<NSCopying>)key
{
    if (!nonNullObject || !key) {
        return;
    }
    [self setObject:nonNullObject forKey:key];
}

- (void)setSafeValue:(id)value forKey:(NSString *)key
{
    if (!value || !([key length]>0)) {
        return;
    }
    [self setValue:value forKey:key];
}

- (id)safeObjectForKey:(id)aKey{
    id object = [self objectForKey:aKey];
    if ([object isKindOfClass:[NSNull class]] || object==NULL || object==nil) {
        return @"";
    }
    return object;
}

@end
