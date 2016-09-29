//
//  SMNSMutableArray.m
//  SMCoreComponent
//
//  Created by sofmen on 23/01/15.
//  Copyright (c) 2015 sofmen. All rights reserved.
//

#import "SMNSMutableArray.h"

@implementation SMNSMutableArray

- (id)safeObjectAtIndex:(NSInteger)index{
    id object = nil;
    if (index<[self count] && index!=NSNotFound) {
        if (![[self safeObjectAtIndex:index] isKindOfClass:[NSNull class]] && [self safeObjectAtIndex:index]!=NULL) {
            object = [self safeObjectAtIndex:index];
        }
    }
    return object;
}
- (void)insertSafeObject:(id)anObject atIndex:(NSUInteger)index{
    
}

@end
