//
//  UpdateCertAndTrainingCell.h
//  SitterApp
//
//  Created by Vikram gour on 08/05/15.
//  Copyright (c) 2015 Sofmen. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface UpdateCertAndTrainingCell : UITableViewCell
@property (weak, nonatomic) IBOutlet UILabel *lbl_certType;
@property (weak, nonatomic) IBOutlet UISwitch *sw_certType;
@property (weak, nonatomic) IBOutlet UIButton *btn_calender;
@property (weak, nonatomic) IBOutlet UILabel *lbl_certDate;

@end
