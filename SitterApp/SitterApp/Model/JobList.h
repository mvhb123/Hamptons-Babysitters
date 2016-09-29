//
//  JobList.h
//  SitterApp
//
//  Created by Shilpa Gade on 03/06/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface JobList : NSObject
@property(nonatomic,strong)NSMutableArray *array_OpenJob;
@property(nonatomic,strong)NSMutableArray *array_ScheduledJob;
@property(nonatomic,strong)NSMutableArray *array_CompletedJob;
@property(nonatomic,strong)NSMutableArray *array_ActiveJob;
@property(nonatomic,strong)NSMutableArray *array_ClosedJob;
@property(nonatomic,strong)NSMutableArray *array_CancelledJob;

@end
