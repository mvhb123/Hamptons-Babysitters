//
//  NSMutableArray+Safe.h
//  SMCoreComponent
//
//  Created by sofmen on 21/02/15.
//  Copyright (c) 2015 sofmen. All rights reserved.
//
#import <Foundation/Foundation.h>

@interface NSMutableArray (Safe)
- (void)insertSafeObject:(id)anObject atIndex:(NSUInteger)index;
- (void)safeSortUsingDescriptor:(NSSortDescriptor *)sortDescriptor;
- (void)safeAddObject:(id)object;
- (id)safeObjectAtIndex:(NSInteger)index;
@end
