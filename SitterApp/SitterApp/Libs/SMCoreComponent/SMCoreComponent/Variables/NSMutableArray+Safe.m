//
//  NSMutableArray+Safe.m
//  SMCoreComponent
//
//  Created by sofmen on 21/02/15.
//  Copyright (c) 2015 sofmen. All rights reserved.
//

#import "NSMutableArray+Safe.h"



@implementation NSMutableArray (Safe)
- (void)insertSafeObject:(id)anObject atIndex:(NSUInteger)index
{
    if (anObject!=nil && (index < ([self count]+1))) {
        [self insertObject:anObject atIndex:index];
    }

}

- (void)safeAddObject:(id)object
{
    if (! object) {
        return;
    }
    [self addObject:object];
}


- (void)safeSortUsingDescriptor:(NSSortDescriptor *)sortDescriptor
{
    NSArray *sortDescriptors = sortDescriptor ? [NSArray arrayWithObject:sortDescriptor] : nil;
    if (sortDescriptors.count > 0) {
        [self sortUsingDescriptors:sortDescriptors];
    }
}

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
