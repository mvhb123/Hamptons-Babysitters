//
//  NSArray+Safe.m
//  SMCoreComponent
//
//  Created by Anubhav Saxena on 24/02/15.
//  Copyright (c) 2015 sofmen. All rights reserved.
//

#import "NSArray+Safe.h"

@implementation NSArray (Safe)

- (id)safeObjectAtIndex:(NSInteger)index{
    id object = nil;
    if (index<[self count] && index!=NSNotFound) {
        if (![[self objectAtIndex:index] isKindOfClass:[NSNull class]] && [self objectAtIndex:index]!=NULL) {
            object = [self objectAtIndex:index];
        }
    }
    return object;
}

@end
